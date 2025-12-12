import os
import subprocess
import re
import sys
import json
import requests
from pathlib import Path

MSG_RE = re.compile(
    r'^(installed|updated|deleted) plugin (.+) version ([^ ]+)$'
)

def get_last_commit_sha():
    return subprocess.check_output(
        ["git", "rev-parse", "HEAD"], text=True
    ).strip()

def get_commit_msg(sha):
    return subprocess.check_output(
        ["git", "log", "-1", "--format=%s", sha], text=True
    ).strip()

def parse_commit_message(msg):
    """
    installed plugin Foo Bar version 1.2.3
    """
    m = MSG_RE.match(msg)
    if not m:
        return None, None, None
    action, name, version = m.groups()
    return action, name, version

def get_plugin_slug_from_commit(sha):
    out = subprocess.check_output(
        ["git", "show", "--name-only", "--pretty=", sha, "--", "wp-content/plugins"],
        text=True,
    )
    first_path = next((l for l in out.splitlines() if l.strip()), "")
    if not first_path:
        return None
    parts = Path(first_path).parts
    if len(parts) < 3:
        return None
    return parts[2]  # wp-content/plugins/<slug>/...

def call_wpscan(slug, version, token):
    headers = {"Authorization": f"Token token={token}"}
    url = f"https://wpscan.com/api/v3/plugins/{slug}/{version}"
    r = requests.get(url, headers=headers, timeout=15)
    # No existe / sin datos
    if r.status_code == 404:
        return {"slug": slug, "version": version, "found": False, "vulnerabilities": []}
    r.raise_for_status()
    data = r.json()
    vulns = data.get(slug, {}).get("vulnerabilities", [])
    return {"slug": slug, "version": version, "found": True, "vulnerabilities": vulns}

def main():
    token = os.getenv("WPSCAN_TOKEN")
    if not token:
        print("WPSCAN_TOKEN no definido; saliendo de forma limpia.")
        return 0

    sha = get_last_commit_sha()
    msg = get_commit_msg(sha)
    print(f"Commit: {sha}")
    print(f"Mensaje: {msg}")

    action, name, version = parse_commit_message(msg)
    slug = get_plugin_slug_from_commit(sha)

    print(f"Action : {action}")
    print(f"Name   : {name}")
    print(f"Version: {version}")
    print(f"Slug   : {slug}")

    if not action or not version or not slug:
        print("Algún campo vacío; no se llama a la API.")
        return 0

    if action not in ("installed", "updated"):
        print(f"Acción '{action}' no requiere consulta a la API.")
        return 0

    print(f"Llamando a WPScan para {slug}@{version}...")

    try:
        result = call_wpscan(slug, version, token)
    except Exception as e:
        print(f"Error llamando a WPScan: {e}")
        return 0  # que no rompa el job

    print("Resultado WPScan:")
    print(json.dumps(result, indent=2))

    # os.makedirs("reports/sca_delta", exist_ok=True)
    # with open("reports/sca_delta/wpscan_delta.json", "w") as f:
    #     json.dump(result, f, indent=2)

    return 0

if __name__ == "__main__":
    sys.exit(main())

