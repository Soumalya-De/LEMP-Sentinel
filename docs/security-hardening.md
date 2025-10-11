# Security Hardening (Repository)

This is a quick checklist to reduce risk on GitHub.

## Branch protection (Settings → Branches)
- Protect the default branch (e.g., `main`).
- Require pull request reviews (1+ approver).
- Require status checks to pass before merging (CI).
- Require linear history (optional) and signed commits (optional).
- Disallow force pushes and deletions.

## Secret hygiene
- Keep `.env` local-only (already ignored by git).
- Pre-commit: run `gitleaks` and `detect-secrets` (configured).
- CI: secret scan runs on every push/PR.
- If a secret leaks, rotate it and invalidate the exposed credential.

## Dependabot
- Enabled for GitHub Actions and Docker images (weekly).

## Supply chain checks
- SBOM (Syft) generated on CI, uploaded as an artifact.
- Trivy scans images: informational on PRs; blocking on main/nightly.

## Access control
- Use least privilege permissions for collaborators.
- Enable 2FA on GitHub accounts.

## Release hygiene (optional)
- Tag releases and attach SBOM artifacts.
- Consider signing images or producing provenance (SLSA) for production.
