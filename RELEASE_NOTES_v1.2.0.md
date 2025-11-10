# Release Notes - v1.2.0

**Release Date:** November 10, 2025  
**Focus:** Final polish - container hardening, documentation consolidation, project completion

---

## Summary

This release marks the completion of LEMP-Sentinel's core development. The project is now in maintenance mode, focusing on security updates and bug fixes rather than new features.

**Timeline:** August 2025 - November 10, 2025 (3 months)  
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

### New Documents
- **CVE Remediation Playbook** (`docs/cve-playbook.md`) - Operational procedures for CVE response
- Added production deployment guide to `docs/configuration.md`

### Consolidated Documentation
- Removed `CVE-REMEDIATION-SUMMARY.md` (content merged into `docs/cve-remediation.md`)
- Simplified all documentation to be more realistic (removed AI-generated fluff)
- Fixed broken cross-references (security.md → security-hardening.md, etc.)
- Reduced `configuration.md` from 1095 to 595 lines
- Reduced `ci-cd-workflows.md` from 613 to 106 lines
- Reduced `cve-playbook.md` from 437 to 160 lines

### Updated Files
- `README.md` - Added project status badge, updated documentation links
- `CONTRIBUTING.md` - Added maintenance mode notice
- `SECURITY.md` - Updated to v1.2.0 status
- All docs now have clear purpose statements and cross-references

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

**Modified:**
- `php/Dockerfile`
- `nginx/Dockerfile`
- `docker-compose.prod.yml`
- `docs/cve-playbook.md` (new)
- `docs/configuration.md`
- `docs/ci-cd-workflows.md`
- `docs/security-hardening.md`
- `docs/cve-remediation.md`
- `docs/architecture.md`
- `docs/quickstart.md`
- `README.md`
- `CONTRIBUTING.md`
- `SECURITY.md`
- `.gitignore`

**Deleted:**
- `CVE-REMEDIATION-SUMMARY.md`

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
