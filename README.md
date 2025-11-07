# LEMP-Sentinel: Production-Ready DevSecOps Stack

<p align="center">
  <img src="images/LEMP-GitHub-Cover-Banner.png" alt="LEMP Stack Banner" width="800" style="max-width: 100%; height: auto;">
</p>

A security-first, fully containerized LEMP (Linux, Nginx, MySQL, PHP) stack with integrated monitoring, active CVE remediation, and GitOps-driven CI/CD workflows. Purpose-built for DevOps/SRE learning, local development, and production-aligned architectures with zero-to-hero automation—from container orchestration to supply chain security.

[![CI Pipeline](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/ci.yml/badge.svg)](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/ci.yml)
[![Security Scan](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/security-scan-fixed.yml/badge.svg)](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/security-scan-fixed.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

---

## Features

- **🐳 Docker-Based**: Fully containerized with Docker Compose orchestration
- **🔒 Security-First**: Pre-commit secret scanning, vulnerability scans, SBOM generation, security headers, rate limiting
- **🤖 Active CVE Remediation**: Automated weekly CVE monitoring with auto-issue creation, minimal human intervention, and proactive remediation when fixes become available
- **📊 Built-In Monitoring**: Uptime Kuma for real-time service health tracking
- **🚀 CI/CD Automated**: 6 GitHub Actions workflows for testing, scanning, CVE monitoring, and validation
- **💾 Backup/Restore**: Automated database backup and restore procedures
- **🔧 Development-Ready**: Hot-reload PHP files, Adminer for database management
- **📦 Supply Chain Security**: Dependabot auto-updates, image digest pinning, weekly CVE status checks
- **🎯 Production-Aligned**: Same codebase for dev/staging/production with env variables
- **🛡️ Hardened by Default**: XSS protection, clickjacking prevention, modern MySQL authentication

---

## Quick Start

Get up and running in 5 minutes:

```bash
# Clone the repository
git clone https://github.com/Soumalya-De/LEMP-Sentinel.git
cd LEMP-Sentinel

# Configure environment
cp .env.example .env

# Start the stack
docker compose up -d

# Verify services
docker compose ps

# Access application
curl http://localhost:8080
```

**Access Points**:
- Application: http://localhost:8080
- Uptime Kuma: http://localhost:3001
- Adminer (dev): http://localhost:8081

See the [**Quickstart Guide**](docs/quickstart.md) for detailed setup instructions.

---

## Documentation

| Document | Description |
|----------|-------------|
| [**Architecture**](docs/architecture.md) | System design, components, network architecture, data flow, system architecture diagram |
| [**Quickstart Guide**](docs/quickstart.md) | Platform-specific setup, verification steps, Uptime Kuma monitoring setup with screenshots, Adminer database UI |
| [**Configuration**](docs/configuration.md) | Environment variables, Docker Compose options, customization |
| [**CI/CD Workflows**](docs/ci-cd-workflows.md) | GitHub Actions pipelines, Dependabot, SBOM, security scanning |
| [**Secrets Management**](docs/secrets.md) | Environment variables, credential storage, pre-commit hooks |
| [**Security Hardening**](docs/security-hardening.md) | Security best practices and hardening recommendations |
| [**CVE Remediation**](docs/cve-remediation.md) | Active CVE management strategy, monitoring, and remediation process |

---

## Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Web Server** | Nginx | alpine (latest) |
| **Runtime** | PHP-FPM | 8.2-fpm-alpine |
| **Database** | MySQL | 8.0 |
| **Container** | Docker | Engine 24+ |
| **Orchestration** | Docker Compose | v2 |
| **Monitoring** | Uptime Kuma | 1.x |
| **DB Admin** | Adminer | 4.x |

---

## Project Structure

```
LEMP-Sentinel/
├── .github/
│   ├── workflows/              # CI/CD pipelines (6 workflows)
│   │   ├── ci.yml              # Main CI: secrets, build, test, SBOM
│   │   ├── security-scan-fixed.yml  # Security scanning with SARIF
│   │   ├── nightly-trivy-scan.yml   # Nightly vulnerability scans
│   │   ├── build-and-pin-php-patched.yml  # Digest pinning
│   │   ├── db-backup-restore.yml    # Backup validation
│   │   └── cve-remediation-monitor.yml  # Weekly CVE monitoring
│   ├── ISSUE_TEMPLATE/         # Issue templates
│   │   ├── bug_report.md       # Bug report template
│   │   ├── config.yml          # Issue template configuration
│   │   ├── cve-tracking.md     # CVE suppression tracking
│   │   ├── feature_request.md  # Feature request template
│   │   └── security_vulnerability.md  # Security vulnerability report
│   ├── pull_request_template.md # PR checklist template
│   └── dependabot.yml          # Automated dependency updates
├── backup/                     # Backup files (gitignored)
├── docs/                       # Comprehensive documentation
│   ├── architecture.md         # System design and data flow
│   ├── quickstart.md           # Platform-specific setup
│   ├── configuration.md        # Environment variables
│   ├── ci-cd-workflows.md      # GitHub Actions documentation
│   ├── secrets.md              # Secrets management guide
│   ├── security-hardening.md   # Security best practices
│   └── cve-remediation.md      # CVE remediation strategy
├── images/                     # Screenshots and diagrams
│   ├── LEMP-GitHub-Cover-Banner.png  # Repository banner
│   ├── system-architecture.png       # Complete system architecture
│   ├── request-data-flow.png         # Request lifecycle diagram
│   ├── dashboard-connected.png       # Dashboard verification
│   ├── adminer-db-overview.png       # Adminer database UI
│   ├── test-db-success.png           # Database test success
│   ├── kuma-monitor-config-http.png  # Uptime Kuma HTTP monitor
│   ├── kuma-monitor-mysql-tcp.png    # Uptime Kuma MySQL monitor
│   └── kuma-telegram-alerts.png      # Uptime Kuma alert config
├── mysql/
│   └── init.sql                # Database schema
├── nginx/
│   ├── Dockerfile              # Custom Nginx image
│   ├── nginx.conf              # Main Nginx config (rate limiting)
│   └── default.conf            # Virtual host configuration
├── php/
│   ├── Dockerfile              # Custom PHP-FPM image
│   └── php.ini                 # Runtime configuration
├── scripts/
│   ├── bcrypt.php              # Password hashing utility
│   └── check-cve-remediation.sh # Weekly CVE status checker
├── www/                        # Application files
│   ├── index.php               # Homepage
│   ├── info.php                # PHP diagnostics (dev only)
│   └── test-db.php             # Database test (dev only)
├── docker-compose.yml          # Service orchestration (main)
├── docker-compose.nonroot.yml  # Non-root PHP override
├── docker-compose.prod.yml     # Production overrides (digest pinning)
├── Makefile                    # Common task automation
├── .env.example                # Environment template
├── .gitignore                  # Git ignore rules
├── .trivyignore                # CVE suppressions (actively monitored)
├── .pre-commit-config.yaml     # Pre-commit hooks
├── CONTRIBUTING.md             # Contribution guidelines
├── SECURITY.md                 # Security policy
├── RELEASE_NOTES_v1.0.0.md     # v1.0.0 release documentation
├── RELEASE_NOTES_v1.1.0.md     # v1.1.0 release documentation
├── CVE-REMEDIATION-SUMMARY.md  # CVE strategy implementation summary
├── LICENSE                     # MIT License (code)
└── LICENSE-DOCS                # CC-BY-4.0 License (docs)
```

---

## Security

This project implements multiple layers of security:

- **Pre-Commit Scanning**: Gitleaks and detect-secrets prevent credential leaks
- **Vulnerability Scanning**: Trivy scans Docker images for known CVEs
- **Active CVE Remediation**: Automated weekly monitoring with GitHub Actions, auto-creates tracking issues, alerts when fixes are available, and requires minimal human intervention to apply patches
- **SBOM Generation**: Complete software bill of materials for supply chain audits
- **Nightly Security Scans**: Automated vulnerability detection with GitHub Issues
- **Network Isolation**: Internal services not exposed to host
- **Secrets Management**: All credentials in `.env` (gitignored)
- **Security Headers**: XSS, clickjacking, and MIME-sniffing protection via Nginx
- **Rate Limiting**: DoS and brute force prevention (10 req/s general, 5 req/s PHP with burst allowances)
- **Modern Authentication**: MySQL 8.0 authentication (development uses mysql_native_password, production should use caching_sha2_password with SSL)

See [**CVE Remediation Strategy**](docs/cve-remediation.md), [**Secrets Management Guide**](docs/secrets.md), and [**Security Hardening**](docs/security-hardening.md) for comprehensive security practices.

---

## Troubleshooting

### Common Issues

**Nginx container restarting with "zero size shared memory zone" error:**
```bash
# Check logs
docker logs lemp_nginx --tail 20

# Fix: Rebuild Nginx image
docker compose stop nginx
docker compose rm -f nginx
docker compose build --no-cache nginx
docker compose up -d nginx
```

**"Command 'php' not found" on host system:**
- This is expected! PHP runs inside the Docker container, not on your host
- To check PHP syntax: `docker exec lemp_php php -l /var/www/html/index.php`
- To run PHP commands: `docker exec lemp_php php [command]`

**Trivy not found when running CVE check script:**
- This is normal for local development
- Trivy runs automatically in GitHub Actions (weekly monitoring)
- To install locally (optional): See https://aquasecurity.github.io/trivy/latest/getting-started/installation/

**Containers not starting:**
```bash
# Check container status
docker compose ps

# View logs
docker compose logs [service_name]

# Validate configuration
docker compose config
```

---

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

### Development Workflow

```bash
# Start with dev profile (includes Adminer)
make up-dev

# View logs
make logs

# Run tests (when available)
make test

# Stop services
make down
```

---

## License

This project is licensed under the MIT License - see [LICENSE](LICENSE) for details.

Documentation is licensed under CC-BY-4.0 - see [LICENSE-DOCS](LICENSE-DOCS).

---

## Acknowledgments

- [Docker](https://www.docker.com/) - Container platform
- [Uptime Kuma](https://github.com/louislam/uptime-kuma) - Monitoring solution
- [Trivy](https://github.com/aquasecurity/trivy) - Vulnerability scanner
- [Gitleaks](https://github.com/gitleaks/gitleaks) - Secret scanner

---

## Support

- **Issues**: [GitHub Issues](https://github.com/Soumalya-De/LEMP-Sentinel/issues)
- **Security**: See [SECURITY.md](SECURITY.md)

---

<p align="center">
  Made with ❤️ by <a href="https://github.com/Soumalya-De">Soumalya De</a>
</p>
