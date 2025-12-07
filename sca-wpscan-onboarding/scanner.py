import os
import sys
import json
import requests
from typing import Dict, Any, List, Optional, Tuple

WPSCAN_TOKEN = os.getenv("WPSCAN_TOKEN")
if not WPSCAN_TOKEN:
    raise RuntimeError("You must define the WPSCAN_TOKEN environment variable")

# WPScan plugins endpoint (API v3)
BASE_URL = "https://wpscan.com/api/v3/plugins/{}"
HEADERS = {"Authorization": f"Token token={WPSCAN_TOKEN}"}


def normalize_version(ver: str) -> Optional[Tuple[int, ...]]:
    """
    Normalize a version string like '8.14.1' -> (8, 14, 1).
    Non-numeric suffixes are ignored (e.g. '1.2.3-beta' -> (1, 2, 3)).
    Returns None if it cannot be parsed.
    """
    if not ver:
        return None

    parts: List[int] = []
    for raw_part in ver.split("."):
        num = ""
        for ch in raw_part:
            if ch.isdigit():
                num += ch
            else:
                break
        if not num:
            break
        parts.append(int(num))
    return tuple(parts) if parts else None


def is_vuln_relevant(vuln: Dict[str, Any], current_version: str) -> bool:
    """
    Decide if a vulnerability still affects the current version.

    Rules:
      - If fixed_in is None or empty -> treat as still relevant (conservative).
      - If fixed_in > current_version -> relevant.
      - If fixed_in <= current_version -> considered fixed, not counted.
    """
    fixed_in = vuln.get("fixed_in")
    if not fixed_in:
        return True

    cur = normalize_version(current_version)
    fix = normalize_version(fixed_in)
    if not cur or not fix:
        # If we cannot compare versions safely, keep the vulnerability
        return True

    return fix > cur


def find_main_plugin_file(plugin_dir: str) -> Optional[str]:
    """
    Heuristic to find the main plugin file:
    - Scan only .php files in the root of the plugin directory (no subdirs).
    - Return the first file whose first lines contain 'Plugin Name:'.
    """
    for name in os.listdir(plugin_dir):
        if not name.endswith(".php"):
            continue
        path = os.path.join(plugin_dir, name)
        try:
            with open(path, "r", encoding="utf-8", errors="ignore") as f:
                # Read only the first N lines looking for the plugin header
                for _ in range(40):
                    line = f.readline()
                    if not line:
                        break
                    if "Plugin Name:" in line:
                        return path
        except Exception:
            continue

    return None


def extract_plugin_metadata(plugin_dir: str) -> Optional[Dict[str, str]]:
    """
    Extract plugin name and version from its header.
    Returns {'name': ..., 'version': ...} or None if not found.
    """
    main_file = find_main_plugin_file(plugin_dir)
    if not main_file:
        return None

    name = None
    version = None

    try:
        with open(main_file, "r", encoding="utf-8", errors="ignore") as f:
            for _ in range(60):
                line = f.readline()
                if not line:
                    break
                stripped = line.strip("/* \t\n\r")
                if stripped.startswith("Plugin Name:"):
                    name = stripped.split(":", 1)[1].strip()
                elif stripped.startswith("Version:"):
                    version = stripped.split(":", 1)[1].strip()
                if name and version:
                    break
    except Exception:
        return None

    if not version:
        return None

    return {
        "name": name or os.path.basename(plugin_dir),
        "version": version,
    }


def fetch_wpscan_data(slug: str) -> Optional[Dict[str, Any]]:
    """
    Query WPScan API for a given plugin slug.
    Returns the parsed JSON data or None if not found (404).
    Raises RuntimeError on other HTTP errors.
    """
    url = BASE_URL.format(slug)
    resp = requests.get(url, headers=HEADERS, timeout=15)

    if resp.status_code == 404:
        return None
    if resp.status_code != 200:
        raise RuntimeError(f"WPScan API error {resp.status_code}: {resp.text}")

    data = resp.json()

    # Direct format: {"name": "...", "latest_version": "...", "vulnerabilities": [...]}
    if "name" in data or "latest_version" in data or "vulnerabilities" in data:
        return data

    # Indexed format: {"slug": { ... }}
    if slug in data and isinstance(data[slug], dict):
        return data[slug]

    return data


def process_plugin(slug: str, plugin_path: str) -> Dict[str, Any]:
    """
    Process a single plugin:
    - Extract metadata (name, installed version).
    - Fetch WPScan data.
    - Filter vulnerabilities based on installed version and fixed_in.
    """
    meta = extract_plugin_metadata(plugin_path)
    if not meta:
        return {
            "slug": slug,
            "status": "no_metadata",
            "reason": "Could not extract plugin header (name/version)",
        }

    installed_version = meta["version"]
    api_data = fetch_wpscan_data(slug)
    if not api_data:
        return {
            "slug": slug,
            "name": meta["name"],
            "installed_version": installed_version,
            "status": "not_in_wpscan",
            "vulnerabilities": [],
        }

    friendly_name = api_data.get("friendly_name") or api_data.get("name") or meta["name"]
    latest_version = api_data.get("latest_version")
    vulns = api_data.get("vulnerabilities", [])

    relevant_vulns = [v for v in vulns if is_vuln_relevant(v, installed_version)]

    return {
        "slug": slug,
        "name": friendly_name,
        "installed_version": installed_version,
        "latest_version": latest_version,
        "status": "ok",
        "vuln_count_total": len(vulns),
        "vuln_count_relevant": len(relevant_vulns),
        "vulnerabilities_relevant": relevant_vulns,
    }


def main():
    """
    Entry point:
    - Expect one argument: path to wp-content.
    - Iterate over wp-content/plugins/* and process each plugin.
    - Print a JSON array to stdout with the results.
    """
    if len(sys.argv) < 2:
        print("Usage: python scanner.py /path/to/wp-content")
        sys.exit(1)

    wp_content_path = sys.argv[1]
    plugins_path = os.path.join(wp_content_path, "plugins")

    if not os.path.isdir(plugins_path):
        raise RuntimeError(f"'plugins' directory does not exist at {plugins_path}")

    results = []

    for slug in sorted(os.listdir(plugins_path)):
        plugin_dir = os.path.join(plugins_path, slug)
        if not os.path.isdir(plugin_dir):
            continue
        res = process_plugin(slug, plugin_dir)
        results.append(res)

    print(json.dumps(results, indent=2, ensure_ascii=False))


if __name__ == "__main__":
    main()
