# Contributing to LEMP-Sentinel

We welcome contributions that enhance security, improve documentation, or add features aligned with production-grade DevSecOps practices. This guide outlines development workflows, quality standards, and submission requirements.

## 📌 Current Project Status

**🟢 Active (Maintenance Mode)** - Core features are complete and stable. This project intentionally maintains a focused scope: **secure LEMP deployment with automated vulnerability management**.

### What We're Accepting

✅ **Security Fixes**
- CVE patches and security vulnerability resolutions
- Security hardening improvements
- Pre-commit hook enhancements

✅ **Bug Fixes**
- Service reliability issues
- Configuration errors
- Documentation corrections

✅ **Documentation Improvements**
- Clarity enhancements
- Missing details or examples
- Updated best practices

✅ **Dependency Updates**
- Docker image updates
- Security patches
- Alpine/PHP/MySQL/Nginx version bumps

### What We're Not Adding

❌ **New Major Features**
- Full observability stack (Prometheus/Grafana/Loki)
- Additional services beyond LEMP core
- Alternative database systems
- Language/framework changes

❌ **Scope Expansion**
- Container orchestration platforms (Kubernetes, Docker Swarm)
- Multi-region deployment patterns
- Advanced networking configurations

**Why?** Feature creep reduces this project's value as a **learning resource and production starting point**. If you need additional capabilities, consider forking the project or building on top of it.

**Have an idea that doesn't fit?** We encourage forks! This MIT-licensed project is designed to be a foundation you can build upon.

## Prerequisites
- Docker Engine v24+ with Compose v2
- Optional: `pre-commit` (Python) for local hooks
- Optional: VS Code + Remote containers/WSL

## Development Setup

```bash
# Fork and clone the repository
git clone https://github.com/<your-username>/LEMP-Sentinel.git
cd LEMP-Sentinel

# Configure environment variables
cp .env.example .env

# Build and start services (production mode by default)
docker compose up --build -d

# Start with development profile (includes Adminer)
docker compose --profile dev up -d

# Verify deployment
make ps
make smoke
```

## Git Workflow

**Branch Naming Convention:**
Use semantic prefixes: `feat/`, `fix/`, `docs/`, `chore/`, `refactor/`, `ci/`, `security/`

**Commit Messages:**
Follow conventional commits format with imperative mood:
```
fix(ci): guard build args with defaults
feat(monitoring): add Telegram alert integration
docs(security): update CVE remediation workflow
```

**Pull Request Guidelines:**
- Keep changes focused and atomic
- Ensure all CI checks pass before requesting review
- Link related issues using `Closes #123` or `Fixes #456`

## Quality Standards

**Code Validation:**
- PHP: All files must pass syntax validation (`make lint-php`)
- Docker Compose: Configuration must be valid (`docker compose config`)
- Documentation: Update README and diagrams when modifying endpoints or architecture

**Security Requirements:**
- Never commit secrets or credentials
- All sensitive data must use environment variables (`.env` file, gitignored)
- Use pre-commit hooks to prevent accidental credential exposure

## Testing & Validation

**Local Verification:**
```bash
# Run smoke tests to verify core functionality
make smoke

# Check PHP syntax across all files
make lint-php

# Validate Docker Compose configuration
docker compose config
```

**CI Pipeline:**
All pull requests automatically run:
- Pre-commit secret scanning (Gitleaks, detect-secrets)
- Docker Compose validation
- PHP syntax verification
- Container build and smoke tests
- SBOM generation and Trivy vulnerability scans

## Security Practices

**Credential Management:**
- Use `.env` for local development (gitignored)
- Use GitHub Actions secrets for CI/CD workflows
- Never commit API keys, passwords, or tokens

**Vulnerability Reporting:**
Report security issues privately following guidelines in [SECURITY.md](SECURITY.md)

**CVE Remediation:**
Automated weekly monitoring runs via GitHub Actions. When encountering new CVEs:
1. Document using the CVE tracking issue template
2. Reference the [CVE Remediation Strategy](docs/cve-remediation.md)
3. Add temporary suppressions to `.trivyignore` with justification

**Production Considerations:**
- Adminer is for development only—never expose in production
- Review [Security Hardening](docs/security-hardening.md) before deployment

## Pull Request Submission

**Pre-Submission Checklist:**
1. Sync your branch with `main`: `git pull origin main --rebase`
2. Run all local validation checks:
   ```bash
   pre-commit run --all-files
   make lint-php
   docker compose config
   make smoke
   ```
3. Ensure commit messages follow conventional commits format
4. Push your branch and open a pull request against `main`
5. Complete the pull request template (description, motivation, testing, risks)
6. Link related issues using keywords (`Closes #123`, `Fixes #456`)

**Review Process:**
- All CI checks must pass (secret scanning, builds, tests, vulnerability scans)
- At least one maintainer approval required for merge
- Squash commits for cleaner history when merging

## Commit Sign-off (Optional)

If your organization requires Developer Certificate of Origin (DCO), include sign-off in commits:
```
Signed-off-by: Your Name <your.email@example.com>
```

## Support & Questions

For assistance:
- Open a [GitHub Discussion](https://github.com/Soumalya-De/LEMP-Sentinel/discussions) for general questions
- Create an issue with the `question` label for specific technical inquiries
- Review existing [documentation](docs/) and [troubleshooting guides](README.md#troubleshooting)

---

We value contributions that enhance security, improve documentation, or add features aligned with production-grade DevSecOps practices. Thank you for helping make LEMP-Sentinel better.
