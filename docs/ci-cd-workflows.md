# CI/CD Workflows

Automated testing, security scanning, and supply chain validation.

## Workflows

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| **CI Pipeline** | Push, PR | Secret scan, build, smoke tests, SBOM |
| **Security Scan** | Nightly (01:00 UTC) | Vulnerability scanning with SARIF |
| **Nightly Trivy Scan** | Nightly (03:00 UTC) | CVE detection with GitHub Issues |
| **CVE Remediation Monitor** | Weekly (Mon 9AM UTC) | Track suppressed CVEs |
| **Build PHP Patched** | Manual | Build patched PHP images |
| **DB Backup/Restore** | Manual | Validate backup procedures |

## CI Pipeline

**File:** `.github/workflows/ci.yml`  
**Trigger:** Push/PR to main

**Steps:**
1. Secret scan with Gitleaks
2. Build Docker images
3. Start stack
4. Run smoke tests (curl endpoints)
5. Generate SBOM with Syft
6. Scan with Trivy

**Artifacts:** SBOM (30-day retention)

## Security Scans

### Nightly Security Scan
**File:** `.github/workflows/security-scan-fixed.yml`  
**Schedule:** 01:00 UTC daily

Scans nginx and php images, uploads SARIF to GitHub Security tab.

### Nightly Trivy Scan  
**File:** `.github/workflows/nightly-trivy-scan.yml`  
**Schedule:** 03:00 UTC daily

Blocks on HIGH/CRITICAL CVEs. Creates GitHub issues for new findings.

### CVE Remediation Monitor
**File:** `.github/workflows/cve-remediation-monitor.yml`  
**Schedule:** Monday 9AM UTC

Checks if suppressed CVEs in `.trivyignore` are still present. Updates tracking issues.

## Manual Workflows

### Build PHP Patched
**File:** `.github/workflows/build-and-pin-php-patched.yml`

Manually trigger to build custom PHP images with specific patches.

### DB Backup/Restore
**File:** `.github/workflows/db-backup-restore.yml`

Validates backup and restore procedures.

## Pre-commit Hooks

**File:** `.pre-commit-config.yaml`

Local checks before commit:
- Gitleaks (secret detection)
- detect-secrets (credential patterns)

Install: `pre-commit install`

## Dependabot

**File:** `.github/dependabot.yml`

Weekly automated updates for:
- GitHub Actions
- Docker base images

## Common Issues

**CI fails on secret scan:**
- Check commit history for accidentally committed secrets
- If false positive, add to `.gitleaksignore`

**Trivy scan blocks PR:**
- Check `.trivyignore` for suppressed CVEs
- Add new CVEs with justification if needed
- See [CVE Remediation Strategy](cve-remediation.md)

**SARIF upload fails:**
- File must be valid SARIF 2.1.0 format
- Size limit: 10MB
- Unique category per upload

For more issues, see [README Troubleshooting](../README.md#troubleshooting).

## Related Documentation

- [Security Hardening](security-hardening.md)
- [CVE Remediation Strategy](cve-remediation.md)
- [CVE Remediation Playbook](cve-playbook.md)
- [Secrets Management](secrets.md)
- [Configuration](configuration.md)
- [Architecture](architecture.md)
