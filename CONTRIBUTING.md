# Contributing to LEMP-Sentinel

Thanks for your interest in contributing! This document outlines how to set up your environment, propose changes, and meet quality and security bars.

## Prerequisites
- Docker Engine v24+ with Compose v2
- Optional: `pre-commit` (Python) for local hooks
- Optional: VS Code + Remote containers/WSL

## Getting started
```bash
# Fork then clone your fork
# git clone https://github.com/<you>/LEMP-Sentinel.git
cd LEMP-Sentinel
cp .env.example .env

# Start the stack (prod-like by default; Adminer excluded)
docker compose up --build -d

# Include dev-only Adminer if needed
# docker compose --profile dev up -d

# Validate
make ps
make smoke
```

## Branching and commits
- Branch from `main` using prefix: `feat/`, `fix/`, `docs/`, `chore/`, `refactor/`, `ci/`, `security/`
- Write clear commit messages using imperative mood: `fix(ci): guard build args with defaults`
- Keep PRs focused and small when possible

## Code style and quality
- PHP: ensure `php -l` passes for all files (`make lint-php`)
- Compose: `docker compose config` must succeed
- Docs: update README and diagrams if behavior/ports/endpoints change
- No secrets in diffs; `.env` stays local-only

## Tests and validation
- Run `make smoke` locally to verify `/`, `/test-db.php`, and `/info.php` behavior
- CI runs: secret scan, compose validation, PHP lint, build + smoke tests, SBOM + Trivy

## Security
- Never commit secrets; use `.env` locally and GitHub Actions secrets in CI
- Report vulnerabilities privately per `SECURITY.md`
- Adminer is for local dev only; do not expose publicly

## Submitting a PR
1. Ensure your branch is up to date with `main`
2. Run local checks (`pre-commit run --all-files`, `make lint-php`, `docker compose config`)
3. Push and open a PR against `main`
4. Fill out the PR template fields (description, motivation, risks)

## DCO/Sign-off (optional)
If your organization requires it, include a sign-off in commits:
```
Signed-off-by: Your Name <you@example.com>
```

## Questions
Open a discussion or issue with the `question` label if you need help getting started.
