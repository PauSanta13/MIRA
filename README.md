# MIRA (WordPress Security Toolkit)

This repository contains a WordPress codebase plus automation to run basic security checks: DAST locally via Docker Compose, and SAST/SCA via GitHub Actions (no Docker Compose services for SAST or SCA). Each mechanism has distinct entry points and constraints; review them before execution.

## Prerequisites
- Docker and Docker Compose available on the host.
- Pre-created Docker network `wp_net` (run once): `docker network create wp_net`.
- Valid WPScan token (`WPSCAN_TOKEN`) and publicly reachable site URL (`TARGET_URL`).
- Optional, for the SCA delta script: Python 3.12+ with `requests` installed (`pip install requests`).

## Environment variables
Configure these either in a root-level `.env` or in your shell:
```
TARGET_URL=https://your-site.example
WPSCAN_TOKEN=your_wpscan_token
```

## Core components
- `docker-compose.yml`: contains a single service `mira-dast` that runs the ZAP Baseline scan against `TARGET_URL` and writes `reports/dast/mira-dast-report.html`. Volumes are relative (`./zap-wrk` and `./reports/dast`); ensure these directories exist.
- `mira_sca.py`: reads the latest commit message in the form `installed|updated|deleted plugin ... version ...`, derives the plugin slug from the last commit touching `wp-content/plugins`, and queries the WPScan API for vulnerabilities relevant to that version.
- GitHub Actions:  
  - `.github/workflows/mira_dast.yml` schedules or manually runs the DAST container via Docker Compose and commits `reports/dast/mira-dast-report.html` back to `master` if changed.  
  - `.github/workflows/mira_sast.yml` runs Semgrep in a container on pushes to `master` authored by `Mirror Admin`, writing `reports/sast/mira-sast-report.json` and committing it.  
  - `.github/workflows/mira_sca.yml` executes `mira_sca.py` on pushes to `master` (also gated on `Mirror Admin` author) and requires `WPSCAN_TOKEN`.

## Local execution guide
1) Ensure the network exists:  
`docker network create wp_net || true`

2) DAST (ZAP baseline):  
```
docker compose pull mira-dast
docker compose run --rm mira-dast
```

Reports are written to `reports/dast/mira-dast-report.html`. SAST and SCA are currently orchestrated via GitHub Actions rather than Docker Compose.

## WPScan script
To query vulnerabilities for the most recent plugin installation/update:
```
pip install requests
export WPSCAN_TOKEN=your_wpscan_token
python mira_sca.py
```
The script reads the latest commit, extracts plugin name/version, and prints the WPScan API response as JSON to stdout.

## Notes
- Run commands from the repository root after correcting volume paths.
- If you keep the conditional in `.github/workflows/mira.yml`, GitHub Actions will only run when the last commit author is `Mirror Admin`; adjust/remove that guard if inappropriate.
- Set `TARGET_URL` and `WPSCAN_TOKEN` as repository secrets for GitHub Actions; `TARGET_URL` is also required locally.
