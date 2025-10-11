# Security Policy

We take the security of this project seriously. Please follow the guidance below.

## Supported versions
This repository is a learning/reference stack. Security scans (Trivy) run on PRs (informational) and nightly for the default branch (blocking on HIGH/CRITICAL).

## Reporting a vulnerability
- Please DO NOT open a public issue for suspected vulnerabilities.
- Email the maintainer with the subject: "[LEMP-Sentinel] Security Report" and include:
  - Affected component(s)
  - Steps to reproduce
  - Impact assessment (what could an attacker do?)
  - Suggested fix if known
- You will receive an acknowledgment within 72 hours.

## Scope
- Docker images, Compose files, and default configuration in this repository.
- Documentation and example code under `www/`.

## Out of scope
- Third-party images and upstream projects.
- Production deployments outside this repository (e.g., forks, customizations).

## Coordinated disclosure
If a vulnerability is confirmed, we will publish a fix and credit the reporter if requested.
