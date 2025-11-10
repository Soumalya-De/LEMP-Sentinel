# LEMP-Sentinel: Production-Grade DevSecOps Infrastructure

[![CI Pipeline](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/ci.yml/badge.svg)](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/ci.yml)
[![Security Scan](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/security-scan-fixed.yml/badge.svg)](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/security-scan-fixed.yml)
[![CVE Monitoring](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/cve-remediation-monitor.yml/badge.svg)](https://github.com/Soumalya-De/LEMP-Sentinel/actions/workflows/cve-remediation-monitor.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)
![Last Commit](https://img.shields.io/github/last-commit/Soumalya-De/LEMP-Sentinel)
[![GitHub Stars](https://img.shields.io/github/stars/Soumalya-De/LEMP-Sentinel?style=social)](https://github.com/Soumalya-De/LEMP-Sentinel/stargazers)

<p align="center">
  <img src="images/LEMP-GitHub-Cover-Banner.png" alt="LEMP Stack Banner" width="800" style="max-width: 100%; height: auto;">
</p>

**A security-first, containerized LEMP stack with automated CVE remediation and security scanning.** Built to learn DevSecOps practices—container orchestration, CI/CD automation, supply chain security, and monitoring. Good for learning, local development, and production deployments.

## 📌 Project Status

**🟢 Active (Maintenance Mode)** - Core features complete. Maintained for security updates and dependency patches.

**Built:** August-November 2025 (NIELIT Cloud Computing Internship)  
**Use Cases:** Learning resource, portfolio project, starting point for LEMP deployments

### 🎯 What's Different

**Active CVE Management** - Weekly automated vulnerability scans with GitHub Actions. When CVEs are found, tracking issues are auto-created with SLA timelines. Get alerts when patches are available.

**Core Security:**
- 🔒 Pre-commit secret scanning (Gitleaks, detect-secrets)
- 📦 Automated SBOM generation for supply chain tracking
- 🛡️ Network isolation (services don't expose ports to host)
- ⚡ Nginx rate limiting and security headers
- 📊 Uptime Kuma monitoring with Telegram/Discord alerts

---

## ✨ Key Features

### � Security Automation
- **Active CVE Remediation**: Automated weekly vulnerability scanning with Trivy, auto-generated GitHub issues, and severity-based SLA tracking
- **Pre-Commit Protection**: Gitleaks and detect-secrets prevent credentials from reaching version control
- **Supply Chain Security**: Automated SBOM generation, Dependabot updates, and digest-pinned images for reproducibility
- **Security Headers**: XSS protection, clickjacking prevention, MIME-sniffing guards, and rate limiting (10 req/s)

### 🚀 CI/CD & Automation
- **6 GitHub Actions Workflows**: Secret scanning, vulnerability detection, SBOM generation, backup validation, CVE monitoring
- **Environment-Based Configs**: Single codebase for dev/staging/production
- **Health Checks**: Self-healing containers with restart policies

### 📊 Monitoring
- **Real-Time Monitoring**: Uptime Kuma tracks Nginx, PHP-FPM, and MySQL
- **Alerting**: Telegram and Discord notifications
- **Network Isolation**: Internal-only service communication

### 🛠️ Developer Experience
- **5-Minute Setup**: Works on Linux, macOS, and Windows (WSL2)
- **Hot-Reload**: PHP changes without container restarts
- **Database UI**: Adminer for MySQL management (dev profile)
- **Cross-Platform**: Tested on Ubuntu 22.04, macOS 14, Windows 11

---

## 🎓 Why This Project?

Most LEMP tutorials skip security automation. This project treats **security as code** with automated CVE tracking, secrets scanning, and monitoring.

**Good For:**
- 🎯 DevOps/SRE portfolio projects
- 🚀 Secure local development environments
- 📚 Learning DevSecOps practices
- 🏢 Teams wanting dev/prod parity without Kubernetes

**What You'll Learn:**
- Docker Compose orchestration
- GitHub Actions CI/CD (6 workflows)
- Vulnerability management (Trivy, SBOM)
- Monitoring and alerting
- Security hardening

---

## 🚀 Quick Start

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
| [**CVE Remediation Strategy**](docs/cve-remediation.md) | CVE management approach, current status, prevention strategies |
| [**CVE Remediation Playbook**](docs/cve-playbook.md) | Operational procedures, SLA timelines, escalation workflows |

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
│   └── README-FULL-BACKUP.md   # Backup instructions
├── docs/                       # Documentation
│   ├── architecture.md         # System design and data flow
│   ├── ci-cd-workflows.md      # GitHub Actions documentation
│   ├── configuration.md        # Environment variables and deployment
│   ├── cve-playbook.md         # CVE remediation operational procedures
│   ├── cve-remediation.md      # CVE remediation strategy
│   ├── quickstart.md           # Platform-specific setup
│   ├── secrets.md              # Secrets management guide
│   └── security-hardening.md   # Security best practices
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
├── .pre-commit-config.yaml     # Pre-commit hooks
├── .secrets.baseline           # detect-secrets baseline
├── .trivyignore                # CVE suppressions (actively monitored)
├── CONTRIBUTING.md             # Contribution guidelines
├── LICENSE                     # MIT License (code)
├── LICENSE-DOCS                # CC-BY-4.0 License (docs)
├── Makefile                    # Common task automation
├── README.md                   # This file
├── RELEASE_NOTES_v1.0.0.md     # v1.0.0 release documentation
├── RELEASE_NOTES_v1.1.0.md     # v1.1.0 release documentation
├── RELEASE_NOTES_v1.2.0.md     # v1.2.0 release documentation
└── SECURITY.md                 # Security policy
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

See [**CVE Remediation Strategy**](docs/cve-remediation.md), [**CVE Remediation Playbook**](docs/cve-playbook.md), [**Secrets Management Guide**](docs/secrets.md), and [**Security Hardening**](docs/security-hardening.md) for comprehensive security practices.

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
