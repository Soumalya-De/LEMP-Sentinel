# Security Hardening Guide

## Overview

Comprehensive security measures implemented in LEMP-Sentinel.

**Document Purpose:** Implementation reference - what security features are active and how to use them  
**Audience:** Developers and DevOps engineers deploying this stack  
**Related Documents:**
- [Secrets Management](secrets.md) - Environment-specific credential handling
- [CVE Remediation Strategy](cve-remediation.md) - Vulnerability management approach
- [CVE Remediation Playbook](cve-playbook.md) - Operational response procedures

## ✅ Implemented Security Features

### Application Security
- **Nginx Security Headers**: Protects against XSS, clickjacking, and MIME-sniffing attacks
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `X-XSS-Protection: 1; mode=block`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Content-Security-Policy` configured
- **Rate Limiting**: 
  - General endpoints: 10 requests/second (burst 20)
  - PHP endpoints: 5 requests/second (burst 10)
- **Hidden Files Protection**: All dotfiles (`.htaccess`, `.git`, etc.) blocked
- **PHP Hardening**: `expose_php=Off`, `allow_url_fopen=Off`, `allow_url_include=Off`
- **Session Security**: `HttpOnly` and `SameSite=Lax` cookies

### Database Security
- **Modern Authentication**: MySQL `caching_sha2_password` (stronger than legacy `mysql_native_password`)
- **Password Hashing**: Bcrypt for all user passwords (see `scripts/bcrypt.php`)
- **Network Isolation**: MySQL not exposed to host network

### Supply Chain Security
- **Pre-Commit Scanning**: Gitleaks and detect-secrets prevent credential leaks
- **Vulnerability Scanning**: Trivy scans all Docker images for CVEs
- **SBOM Generation**: Complete software bill of materials via Syft
- **Dependabot**: Weekly automated updates for GitHub Actions and Docker images
- **Digest Pinning**: Production images use SHA256 digests in `docker-compose.prod.yml`

### CI/CD Security
- **Secret Scanning**: Runs on every push/PR (gitleaks)
- **Trivy Workflows**: 3 automated workflows (CI, nightly security, nightly critical)
- **SARIF Upload**: GitHub Advanced Security integration
- **Nightly Scans**: Automated vulnerability detection with GitHub Issues

## 🔧 Repository Configuration

### Branch Protection (Settings → Branches)
- Protect the default branch (`main`)
- Require pull request reviews (1+ approver)
- Require status checks to pass before merging (CI)
- Require linear history (optional) and signed commits (optional)
- Disallow force pushes and deletions

### Secret Hygiene
- Keep `.env` local-only (already ignored by git)
- Pre-commit: run `gitleaks` and `detect-secrets` (configured in `.pre-commit-config.yaml`)
- CI: secret scan runs on every push/PR
- If a secret leaks, rotate it and invalidate the exposed credential immediately

### Access Control
- Use least privilege permissions for collaborators
- Enable 2FA on all GitHub accounts
- Review collaborator access quarterly

## 📋 Production Deployment Checklist

Before deploying to production:

- [ ] Change all default passwords in `.env` (use strong 16+ character passwords)
- [ ] Replace placeholder credentials in `mysql/init.sql` with real bcrypt hashes
- [ ] Enable HTTPS (use reverse proxy: Traefik, Caddy, or nginx-proxy with Let's Encrypt)
- [ ] Remove or disable Adminer (use `docker-compose.prod.yml` profile)
- [ ] Configure firewall rules (block direct MySQL/PHP-FPM access)
- [ ] Set up automated backups (`make backup` in cron)
- [ ] Configure Uptime Kuma alerts for monitoring
- [ ] Review and remove `.trivyignore` (after patched base images available)
- [ ] Enable strict Trivy blocking (`--exit-code 1` without suppressions)
- [ ] Verify all security headers are active (test with securityheaders.com)
- [ ] Test rate limiting under load
- [ ] Review MySQL authentication configuration
- [ ] Set up log aggregation and monitoring
- [ ] Document incident response procedures

## 🔒 Known Limitations (Development Environment)

| Limitation | Reason | Production Solution |
|-----------|--------|-------------------|
| No HTTPS | Local dev convenience | Add reverse proxy with SSL/TLS |
| Weak default passwords | Development defaults | Use strong unique passwords |
| Adminer exposed | Dev database management | Remove in production |
| No WAF | Development simplicity | Add Cloudflare/AWS WAF |
| Trivy suppressions | Pending Alpine patches | Remove `.trivyignore` when fixed |

## 📚 Additional Resources

- [Secrets Management Guide](secrets.md)
- [CI/CD Workflows Documentation](ci-cd-workflows.md)
- [Configuration Reference](configuration.md)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [CIS Docker Benchmark](https://www.cisecurity.org/benchmark/docker)
- [Mozilla Web Security Guidelines](https://infosec.mozilla.org/guidelines/web_security)

## 🚨 Security Issues

If you discover a security vulnerability:
- **DO NOT** open a public issue
- Email the maintainer with details
- See [SECURITY.md](../SECURITY.md) for reporting procedures

## 🔄 Release Hygiene

- Tag releases with semantic versioning (v1.0.0, v1.1.0, etc.)
- Attach SBOM artifacts to releases
- Consider signing images or producing provenance (SLSA) for production
- Document security improvements in release notes
- Maintain changelog with security fixes highlighted
