# CI/CD Workflows

Automated testing, security scanning, and supply chain validation.

## Overview

This project includes 5 GitHub Actions workflows for comprehensive CI/CD:

| Workflow | Trigger | Purpose | Badge |
|----------|---------|---------|-------|
| **CI Pipeline** | Push, PR | Secret scan, build, smoke tests, SBOM | ![CI](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/ci.yml/badge.svg) |
| **Security Scan** | Nightly (01:00 UTC) | Vulnerability scanning with SARIF upload | ![Security](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/security-scan-fixed.yml/badge.svg) |
| **Nightly Trivy Scan** | Nightly (02:00 UTC) | Critical CVE detection with GitHub Issues | ![Trivy](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/nightly-trivy-scan.yml/badge.svg) |
| **Build PHP Patched** | Manual | Build and pin patched PHP images | Manual dispatch |
| **DB Backup/Restore** | Manual | Validate backup and restore procedures | Manual dispatch |

## CI Pipeline (ci.yml)

**File**: `.github/workflows/ci.yml`

### Trigger

```yaml
on:
  push:
    branches: [main]
  pull_request:
    branches: [main]
```

### Stages

#### 1. Secret Scan (gitleaks)

Scans commit history for accidentally committed secrets:

```yaml
- name: Run gitleaks
  uses: gitleaks/gitleaks-action@v2
  with:
    args: --verbose --redact
```

**Blocks PR if**: Secrets detected (API keys, passwords, tokens).

#### 2. Build and Start Stack

Builds Docker images and starts services:

```yaml
- name: Build images
  run: docker compose build

- name: Start stack
  run: docker compose up -d

- name: Wait for health
  run: |
    for i in {1..30}; do
      docker compose ps --format json | jq -e '.[] | select(.Health == "healthy")' && break
      sleep 2
    done
```

**Blocks PR if**: Build fails or services don't become healthy within 60s.

#### 3. Smoke Tests

Validates application functionality:

```yaml
- name: Test homepage
  run: |
    curl -f http://localhost:8080 || exit 1

- name: Test database connection
  run: |
    response=$(curl -s http://localhost:8080/test-db.php)
    echo "$response" | grep -q "success" || exit 1
```

**Blocks PR if**: HTTP endpoints unreachable or return errors.

#### 4. SBOM Generation

Creates Software Bill of Materials for supply chain security:

```yaml
- name: Generate SBOM
  uses: anchore/sbom-action@v0
  with:
    format: cyclonedx-json
    output-file: sbom.json
```

**Uploads**: JSON artifact for security audit trails.

#### 5. Trivy Vulnerability Scan

Scans Docker images for known vulnerabilities:

```yaml
- name: Scan images with Trivy
  run: |
    for image in nginx php mysql; do
      trivy image --exit-code 1 \
        --severity HIGH,CRITICAL \
        --format sarif \
        --output trivy-${image}.sarif \
        lemp-stack-${image}:latest
    done
```

**Blocks PR if**: HIGH or CRITICAL vulnerabilities detected (with exceptions in `.trivyignore`).

### Success Criteria

All 5 stages must pass:
- ✅ No secrets detected
- ✅ Images build successfully
- ✅ Services start and become healthy
- ✅ Smoke tests pass (HTTP 200, DB connection)
- ✅ No critical vulnerabilities (or suppressed in `.trivyignore`)

### Artifacts

- `sbom.json`: Software Bill of Materials
- `trivy-*.sarif`: Vulnerability scan reports
- Build logs

## Security Scan Workflow (security-scan-fixed.yml)

**File**: `.github/workflows/security-scan-fixed.yml`

### Trigger

```yaml
on:
  schedule:
    - cron: '0 1 * * *'  # Daily at 01:00 UTC
  workflow_dispatch:
```

### Purpose

Nightly vulnerability scanning with results uploaded to GitHub Security tab (Code Scanning Alerts).

### Key Features

#### 1. Multi-Image Scanning

Scans 4 images:
- `lemp-sentinel-nginx:latest` (custom built)
- `lemp-sentinel-php:latest` (custom built)
- `nginx:alpine` (base image)
- `php:8.2-fpm-alpine` (base image)

#### 2. SARIF Output

Generates SARIF (Static Analysis Results Interchange Format) files:

```yaml
- name: Scan and upload SARIF
  run: |
    images="lemp-sentinel-nginx:latest lemp-sentinel-php:latest nginx:alpine php:8.2-fpm-alpine"
    echo "$images" | tr ' ' '\n' | while read -r img; do
      safe=$(echo "$img" | sed 's#[^A-Za-z0-9._-]#_#g')
      trivy image --format sarif \
        --output "$GITHUB_WORKSPACE/trivy-results/${safe}.sarif" \
        --severity HIGH,CRITICAL \
        "$img" || true
    done
```

**Sanitization**: Image names with `:` and `/` are converted to `_` for valid filenames.

#### 3. GitHub Security Integration

Uploads SARIF files to GitHub Code Scanning via API:

```yaml
- name: Upload SARIF to GitHub Security
  run: |
    for sarif in trivy-results/*.sarif; do
      img_name=$(basename "$sarif" .sarif)
      curl -X POST \
        -H "Authorization: token ${{ secrets.GITHUB_TOKEN }}" \
        -H "Accept: application/vnd.github.v3+json" \
        "https://api.github.com/repos/${{ github.repository }}/code-scanning/sarifs" \
        --data @- <<EOF
    {
      "commit_sha": "${{ github.sha }}",
      "ref": "refs/heads/${{ github.ref_name }}",
      "sarif": "$(base64 -w0 < "$sarif")",
      "checkout_uri": "https://github.com/${{ github.repository }}",
      "started_at": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
      "tool_name": "Trivy",
      "category": "trivy-${img_name}"
    }
    EOF
    done
```

**Unique Categories**: Each image gets a unique category to avoid conflicts in GitHub Security tab.

### Viewing Results

Navigate to: **Repository → Security → Code scanning alerts**

Alerts are grouped by severity:
- **Critical**: Immediate attention required
- **High**: Should be addressed soon
- **Medium/Low**: Review and prioritize

## Nightly Trivy Scan (nightly-trivy-scan.yml)

**File**: `.github/workflows/nightly-trivy-scan.yml`

### Trigger

```yaml
on:
  schedule:
    - cron: '0 2 * * *'  # Daily at 02:00 UTC (1 hour after security-scan)
  workflow_dispatch:
```

### Purpose

Detect CRITICAL vulnerabilities and automatically create GitHub Issues for tracking.

### Key Features

#### 1. Critical-Only Detection

```yaml
- name: Scan for CRITICAL vulnerabilities
  run: |
    trivy image --severity CRITICAL \
      --format json \
      --output trivy-critical.json \
      lemp-sentinel-nginx:latest
```

**Threshold**: Only CRITICAL severity (highest priority).

#### 2. Automatic Issue Creation

If critical vulnerabilities found:

```yaml
- name: Create GitHub Issue
  if: steps.scan.outcome == 'failure'
  uses: actions/github-script@v6
  with:
    script: |
      const fs = require('fs');
      const report = JSON.parse(fs.readFileSync('trivy-critical.json', 'utf8'));
      
      github.rest.issues.create({
        owner: context.repo.owner,
        repo: context.repo.repo,
        title: `🚨 CRITICAL Vulnerabilities Detected - ${new Date().toISOString().split('T')[0]}`,
        body: `Trivy scan found CRITICAL vulnerabilities:\n\n${JSON.stringify(report, null, 2)}`,
        labels: ['security', 'critical', 'automated']
      });
```

**Labels**: `security`, `critical`, `automated` for easy filtering.

### Issue Management

Issues include:
- CVE identifiers
- Affected packages
- Fixed version (if available)
- CVSS score
- References (NVD, vendor advisories)

**Action Required**: Review issue, apply fix, re-run scan, close when resolved.

## Build PHP Patched Workflow (build-and-pin-php-patched.yml)

**File**: `.github/workflows/build-and-pin-php-patched.yml`

### Trigger

```yaml
on:
  workflow_dispatch:
    inputs:
      base_image:
        description: 'Base PHP image to use'
        required: true
        default: 'php:8.2-fpm-alpine3.20'
```

### Purpose

Build custom PHP images with security patches and pin to specific digest for supply chain security.

### Process

1. **Pull Base Image**: Fetch specific PHP-FPM Alpine version
2. **Apply Patches**: Install security updates and extensions
3. **Build Image**: Create `lemp-sentinel-php:patched`
4. **Extract Digest**: Capture SHA256 digest for pinning
5. **Update Compose**: Suggest updating `docker-compose.yml` with digest

### Usage

```yaml
# Current (tag-based, can drift)
image: php:8.2-fpm-alpine

# After workflow (digest-pinned, immutable)
image: php@sha256:abc123...def456
```

**Security Benefit**: Prevents supply chain attacks via tag manipulation.

## DB Backup/Restore Workflow (db-backup-restore.yml)

**File**: `.github/workflows/db-backup-restore.yml`

### Trigger

```yaml
on:
  workflow_dispatch:
  schedule:
    - cron: '0 3 * * 0'  # Weekly on Sundays at 03:00 UTC
```

### Purpose

Validate backup and restore procedures to ensure disaster recovery readiness.

### Stages

#### 1. Create Test Data

```yaml
- name: Seed database
  run: |
    docker compose exec -T mysql mysql -u root -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE <<EOF
    INSERT INTO users (username, email, password_hash) VALUES 
      ('testuser', 'test@example.com', 'hash123');
    EOF
```

#### 2. Perform Backup

```yaml
- name: Backup database
  run: |
    docker compose exec -T mysql sh -c \
      "mysqldump -u root -p\$MYSQL_ROOT_PASSWORD \$MYSQL_DATABASE" \
      > backup-test.sql
```

#### 3. Simulate Disaster

```yaml
- name: Drop database
  run: |
    docker compose exec -T mysql mysql -u root -p$MYSQL_ROOT_PASSWORD \
      -e "DROP DATABASE $MYSQL_DATABASE; CREATE DATABASE $MYSQL_DATABASE;"
```

#### 4. Restore from Backup

```yaml
- name: Restore database
  run: |
    docker compose exec -T mysql sh -c \
      "mysql -u root -p\$MYSQL_ROOT_PASSWORD \$MYSQL_DATABASE" \
      < backup-test.sql
```

#### 5. Verify Data Integrity

```yaml
- name: Verify restore
  run: |
    result=$(docker compose exec -T mysql mysql -u root -p$MYSQL_ROOT_PASSWORD \
      $MYSQL_DATABASE -e "SELECT COUNT(*) FROM users WHERE username='testuser'")
    echo "$result" | grep -q "1" || exit 1
```

**Success Criteria**: Restored data matches original test data.

### Artifacts

- `backup-test.sql`: Test backup file
- Workflow logs with verification results

## Dependabot Configuration

**File**: `.github/dependabot.yml`

### Purpose

Automated dependency updates for Docker base images and GitHub Actions.

### Configuration

```yaml
version: 2
updates:
  # Docker base images in custom Dockerfiles
  - package-ecosystem: "docker"
    directory: "/nginx"
    schedule:
      interval: "weekly"
      day: "monday"
      time: "06:00"
      timezone: "UTC"

  - package-ecosystem: "docker"
    directory: "/php"
    schedule:
      interval: "weekly"

  # GitHub Actions versions
  - package-ecosystem: "github-actions"
    directory: "/"
    schedule:
      interval: "weekly"
```

### What It Does

1. **Weekly Scans**: Every Monday at 06:00 UTC
2. **Detects Outdated Dependencies**:
   - `nginx:alpine` → `nginx:alpine3.20`
   - `php:8.2-fpm-alpine` → `php:8.2-fpm-alpine3.21`
   - `actions/checkout@v3` → `actions/checkout@v4`
3. **Creates Pull Requests**: Automatic PRs with upgrade proposals
4. **CI Validation**: PRs trigger full CI pipeline for testing

### PR Management

Dependabot PRs include:
- Changelog links
- Release notes
- Compatibility notes
- Commit history

**Action Required**: Review PR, verify CI passes, merge or close.

## Trivy Ignore (.trivyignore)

**File**: `.trivyignore`

### Purpose

Temporarily suppress known vulnerabilities with documented rationale.

### Current Suppressions

```bash
# Temporary suppression for libxml2 CVEs in Alpine 3.22.2
# Waiting for patched base image release
CVE-2025-49794  # libxml2 XML parser vulnerability
CVE-2025-49796  # libxml2 buffer overflow
CVE-2025-49795  # libxml2 memory corruption
CVE-2025-6021   # libxml2 denial of service

# TODO: Remove after upgrading to php:8.2-fpm-alpine3.23 or later
# TODO: Re-enable strict blocking (--exit-code 1 without suppressions)
```

### Usage Guidelines

**When to Add Suppressions**:
- ✅ Known false positives
- ✅ Vulnerabilities without available fixes
- ✅ Temporary workarounds with documented timelines

**When NOT to Suppress**:
- ❌ Convenience (ignoring real vulnerabilities)
- ❌ "We'll fix it later" (without tracking)
- ❌ Permanent suppressions (defeats purpose of scanning)

### Best Practices

1. **Document Rationale**: Every suppression needs "why" explanation
2. **Add TODO with Deadline**: Set target for removal
3. **Track in Issues**: Create GitHub Issue for suppressed CVEs
4. **Review Regularly**: Weekly review of `.trivyignore` for stale entries
5. **Monitor Upstream**: Check for patched base images

## SBOM (Software Bill of Materials)

### What It Is

Complete inventory of all software components, versions, and dependencies.

### Generation

Automatic via Anchore SBOM Action:

```yaml
- name: Generate SBOM
  uses: anchore/sbom-action@v0
  with:
    format: cyclonedx-json
    output-file: sbom.json
    upload-artifact: true
```

### Contents

- Package names and versions
- Licenses (SPDX identifiers)
- Dependency tree
- Vulnerabilities (cross-referenced with CVE databases)

### Use Cases

1. **Supply Chain Audits**: Track all components
2. **License Compliance**: Identify GPL/copyleft dependencies
3. **Vulnerability Impact**: "Do we use log4j?"
4. **Procurement Requirements**: SBOM required by some customers/regulations

## Workflow Best Practices

### 1. Branch Protection

Configure in GitHub Settings → Branches → Branch protection rules:

- ✅ Require status checks (CI must pass)
- ✅ Require up-to-date branches (force rebase)
- ✅ Require review from code owners
- ✅ Dismiss stale reviews on new commits

### 2. Secret Management

**Never commit secrets**. Use GitHub Secrets:

```yaml
env:
  REGISTRY_PASSWORD: ${{ secrets.DOCKER_HUB_TOKEN }}
  TELEGRAM_BOT_TOKEN: ${{ secrets.TELEGRAM_TOKEN }}
```

Add secrets: **Settings → Secrets and variables → Actions → New repository secret**

### 3. Artifact Retention

Adjust retention policy to balance storage costs:

```yaml
- name: Upload SBOM
  uses: actions/upload-artifact@v3
  with:
    name: sbom
    path: sbom.json
    retention-days: 90  # Customize based on compliance needs
```

### 4. Workflow Optimization

- **Cache Docker layers**: Use `docker/build-push-action` with caching
- **Matrix builds**: Test multiple PHP/MySQL versions in parallel
- **Conditional steps**: Skip non-critical steps on PR (only run on main)

### 5. Monitoring Workflow Health

Track workflow metrics:
- **Success Rate**: Should be >95%
- **Duration**: CI should complete in <5 minutes
- **Flakiness**: Investigate intermittent failures

## Troubleshooting Workflows

### Workflow Not Triggering

**Check**:
- YAML syntax (use `yamllint` or GitHub workflow validator)
- Branch protection rules (workflows disabled on protected branches)
- GitHub Actions permissions (Settings → Actions → General)

### Secrets Not Available

**Check**:
- Secret name matches exactly (case-sensitive)
- Secret available at repository/organization level
- Branch has access (if using environment secrets)

### Trivy Scan Failing

**Check**:
- `.trivyignore` is properly formatted
- Trivy version compatibility (`--format sarif` requires v0.42+)
- Network connectivity (Trivy downloads vulnerability DB)

### SARIF Upload Rejected

**Check**:
- File is valid SARIF 2.1.0 format
- File size <10MB (GitHub limit)
- Unique category per upload (no duplicates)

See [Troubleshooting Guide](troubleshooting.md) for comprehensive solutions.

## Related Documentation

- [Security Practices](security.md) - Security hardening and scanning
- [Monitoring](monitoring.md) - Uptime Kuma and alerting
- [Architecture](architecture.md) - System design and components
- [Troubleshooting](troubleshooting.md) - Common issues and solutions
