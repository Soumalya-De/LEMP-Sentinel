# Release Notes - v1.2.0

**Release Date:** November 10, 2025  
**Focus:** Final polish - container hardening, documentation consolidation, project completion

---

## Summary

This release marks the completion of LEMP-Sentinel's core development. The project is now in maintenance mode, focusing on security updates and bug fixes rather than new features.

**Timeline:** August 2025 - November 2025 (3 months)  
**Context:** NIELIT Cloud Computing Internship mini project

---

## Security Enhancements

### Container Hardening
- **Non-root users**: PHP runs as uid 1000, Nginx as nginx user
- **Capability dropping**: Removed ALL capabilities, added only required ones
- **Read-only filesystems**: Containers use read-only FS with tmpfs for writable dirs
- **Security options**: Added `no-new-privileges` to all services

**Files Modified:**
- `php/Dockerfile` - Added appuser (uid 1000)
- `nginx/Dockerfile` - Explicit nginx user
- `docker-compose.prod.yml` - Security options, cap_drop, read_only FS

---

## Documentation

### Major Documentation Overhaul
**Goal:** Remove AI-generated fluff to make documentation sound authentic and human-written

**Changes Summary:**
- Simplified language across all files (removed corporate buzzwords, formal patterns)
- Removed ~1,500 lines of unnecessary enterprise detail
- Fixed all broken cross-references
- Added clear document purposes

### New Documents
- **CVE Remediation Playbook** (`docs/cve-playbook.md`) - Operational procedures for CVE response (437→160 lines after simplification)
- **RELEASE_NOTES_v1.2.0.md** - This file

### Consolidated/Simplified Documentation
- **Deleted:** `CVE-REMEDIATION-SUMMARY.md` (duplicate content merged into `docs/cve-remediation.md`)
- **Simplified:** `configuration.md` (1095→595 lines, 45% reduction) - Removed 450-line production section with excessive code examples
- **Simplified:** `ci-cd-workflows.md` (613→106 lines, 83% reduction) - Removed verbose YAML examples
- **Simplified:** `cve-playbook.md` (437→160 lines, 63% reduction) - Removed enterprise KPIs, metrics, detailed timelines
- **Simplified:** `security-hardening.md` - Removed "Release Hygiene" section
- **Simplified:** `CONTRIBUTING.md` - More casual, direct language
- **Simplified:** `quickstart.md` - Removed verbose explanations
- **Simplified:** `cve-remediation.md` - More conversational tone
- **Simplified:** `README.md` - Removed marketing speak, enterprise buzzwords

### Fixed Cross-References
- Fixed: `security.md` → `security-hardening.md` (in architecture.md, quickstart.md, ci-cd-workflows.md)
- Fixed: Removed references to deleted `monitoring.md` and `troubleshooting.md`
- Updated: All documentation now accurately cross-references existing files

### Updated Files
- `README.md` - Project status, simplified language, added RELEASE_NOTES_v1.2.0.md to structure
- `CONTRIBUTING.md` - Maintenance mode notice, casual tone
- `SECURITY.md` - v1.2.0 status, corrected false claims
- `docs/architecture.md` - Fixed broken links
- `docs/quickstart.md` - Simplified language
- `docs/secrets.md` - Added document purpose
- All docs now have clear purpose statements

---

## Project Status

**Current State:** Active (Maintenance Mode)

**What This Means:**
- ✅ Accepting: Security fixes, bug fixes, documentation improvements, dependency updates
- ❌ Not accepting: New major features, scope expansion

**Rationale:** Core functionality is complete. Keeping the project focused makes it useful as a learning resource and production starting point.

---

## Migration from v1.1.0

No breaking changes. This release only adds security hardening and improves documentation.

**To upgrade:**
```bash
git pull origin main
docker compose down
docker compose --profile prod build --no-cache
docker compose --profile prod up -d
```

---

## Known Issues

- 4 libxml2 CVEs suppressed (awaiting Alpine 3.23 release)
- See `.trivyignore` for current CVE status

---

## Files Changed

### Container Hardening
- `php/Dockerfile` - Added non-root user (uid 1000)
- `nginx/Dockerfile` - Explicit nginx user
- `docker-compose.prod.yml` - Security options (NEW FILE)

### Documentation - New Files
- `docs/cve-playbook.md` - CVE remediation operational procedures
- `RELEASE_NOTES_v1.2.0.md` - This release notes file

### Documentation - Major Rewrites (>50% content reduction)
- `docs/configuration.md` - Simplified production section (1095→595 lines)
- `docs/ci-cd-workflows.md` - Removed verbose YAML (613→106 lines)
- `docs/cve-playbook.md` - Removed enterprise patterns (437→160 lines)

### Documentation - Simplified
- `README.md` - Removed marketing speak, added v1.2.0 structure entry
- `CONTRIBUTING.md` - Casual tone, maintenance notice
- `SECURITY.md` - Fixed false claims, v1.2.0 status
- `docs/architecture.md` - Fixed broken links
- `docs/quickstart.md` - Simplified language
- `docs/cve-remediation.md` - More conversational
- `docs/security-hardening.md` - Removed release hygiene section
- `docs/secrets.md` - Added document purpose

### Configuration
- `.gitignore` - Added private documentation files

### Deleted Files
- `CVE-REMEDIATION-SUMMARY.md` - Duplicate content (merged into cve-remediation.md)

---

## Contributors

- **Soumalya De** (@Soumalya-De) - All implementation and documentation

---

## Next Steps

### For Users
- Review [Security Hardening Guide](docs/security-hardening.md)
- Check [Production Deployment](docs/configuration.md#production-deployment)
- See [CVE Remediation Strategy](docs/cve-remediation.md) for vulnerability management

### For Contributors
- See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines
- Report security issues via [SECURITY.md](SECURITY.md)
- Fork the project for custom needs (MIT licensed)

---

## Acknowledgments

- **ChatGPT** - AI assistance in implementation (transparently documented)
- **NIELIT** - Cloud Computing Internship program
- **Open Source Community** - Trivy, Docker, GitHub Actions, Alpine Linux

---

**Project Status:** 🟢 Active (Maintenance Mode)  
**Version:** v1.2.0  
**Released:** November 10, 2025
