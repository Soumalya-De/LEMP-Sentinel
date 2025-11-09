# Security Policy

LEMP-Sentinel implements enterprise-grade security practices for containerized infrastructure. This document outlines our vulnerability management process and reporting procedures.

---

## Supported Versions

**Current Status:** Active (Maintenance Mode) - v1.2.0  
**Security Scanning:** Automated Trivy vulnerability scans run on:
- Pull requests (informational warnings)
- Nightly scans (daily at 03:00 UTC, blocks on HIGH/CRITICAL findings)
- Weekly CVE remediation monitoring with auto-generated tracking issues

**Versioning:** This project follows [Semantic Versioning](https://semver.org/). Security patches are applied to the latest stable release.

---

## Reporting a Vulnerability

**⚠️ DO NOT open public issues for security vulnerabilities.**

### Responsible Disclosure Process

1. **Submit a Private Report:**
   - Use [GitHub Security Advisories](https://github.com/Soumalya-De/LEMP-Sentinel/security/advisories/new) (preferred)
   - Or create a private issue by contacting the repository maintainer via GitHub

2. **Include the Following Information:**
   - **Affected Component:** Docker image, configuration file, or code module
   - **Vulnerability Type:** SQL injection, command injection, privilege escalation, etc.
   - **Attack Vector:** Steps to reproduce the issue
   - **Impact Assessment:** Potential damage or data exposure
   - **Suggested Remediation:** Proposed fix or mitigation (if known)
   - **Discovery Method:** How the vulnerability was identified

3. **Expected Response Timeline:**
   - **Initial Acknowledgment:** Within 72 hours
   - **Triage & Assessment:** Within 7 days
   - **Fix Development:** Based on severity (see SLAs below)
   - **Public Disclosure:** Coordinated with reporter after patch release

---

## Severity Levels & SLAs

| Severity | Description | Response Time | Example |
|----------|-------------|---------------|---------|
| **CRITICAL** | Remote code execution, authentication bypass | 7 days | Unpatched RCE in PHP-FPM |
| **HIGH** | Privilege escalation, data leakage | 14 days | SQL injection in application code |
| **MEDIUM** | DoS, information disclosure | 30 days | Exposed debug endpoints |
| **LOW** | Minor configuration issues | 60 days | Verbose error messages |

---

## Scope

### In Scope (We Accept Reports For)

✅ Docker images and Dockerfiles (`nginx/Dockerfile`, `php/Dockerfile`)  
✅ Docker Compose configuration files  
✅ Application code in `www/` directory  
✅ CI/CD workflows (`.github/workflows/`)  
✅ Nginx and PHP configuration files  
✅ Environment variable handling and secrets management  
✅ Supply chain security (base images, dependencies)  

### Out of Scope (We Do NOT Accept Reports For)

❌ Upstream vulnerabilities in official Docker images (report to Alpine, PHP, Nginx, MySQL maintainers)  
❌ Third-party dependencies tracked in `.trivyignore` with active remediation plans  
❌ Vulnerabilities in forked/customized deployments outside this repository  
❌ Social engineering or physical attacks  
❌ Denial of service requiring extraordinary resources  

**Note:** CVEs in base images (e.g., `php:8.2-fpm-alpine`) are tracked through our [CVE Remediation Playbook](docs/cve-playbook.md) and [CVE Remediation Strategy](docs/cve-remediation.md). Check existing [CVE tracking issues](https://github.com/Soumalya-De/LEMP-Sentinel/labels/cve-tracking) before reporting.

---

## Coordinated Disclosure

Upon confirmation of a vulnerability:

1. **Assessment:** We confirm the vulnerability and determine severity
2. **Fix Development:** We develop and test a fix (coordinating timeline with reporter)
3. **Security Advisory:** Publish a GitHub Security Advisory with:
   - Vulnerability description
   - Affected versions
   - Patched versions
   - Workarounds or mitigations (if available)
   - Credit to the reporter (unless anonymity is requested)
4. **Public Disclosure:** Release the fix and announce in release notes

**Timeline Coordination:** We will work with you to agree on a reasonable disclosure timeline that allows users to patch before public details are released.

**Reporter Credit:** We acknowledge responsible disclosures in release notes and security advisories unless you request anonymity.

---

## Security Best Practices

For secure deployment of LEMP-Sentinel:

- Review [Security Hardening Guide](docs/security-hardening.md)
- Enable [pre-commit secret scanning](docs/secrets.md)
- Follow [CVE Remediation Strategy](docs/cve-remediation.md)
- Never expose Adminer in production environments
- Use TLS/SSL for production deployments
- Regularly update base images and dependencies

---

## Security Resources

- **Documentation:** [docs/security-hardening.md](docs/security-hardening.md)
- **CVE Strategy:** [docs/cve-remediation.md](docs/cve-remediation.md)
- **CI/CD Security:** [docs/ci-cd-workflows.md](docs/ci-cd-workflows.md)
- **Secrets Management:** [docs/secrets.md](docs/secrets.md)

---

---
**Last Updated:** November 10, 2025
