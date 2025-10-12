# LEMP Stack with Integrated Monitoring

<p align="center">
  <img src="images/LEMP-GitHub-Cover-Banner.png" alt="LEMP Stack Banner" width="800" style="max-width: 100%; height: auto;">
</p>

A production-ready LEMP (Linux, Nginx, MySQL, PHP) stack with built-in monitoring, security scanning, and automated CI/CD workflows. Designed for local development with production-aligned architecture.

[![CI Pipeline](https://github.com/Soumalya-De/lemp-stack/actions/workflows/ci.yml/badge.svg)](https://github.com/Soumalya-De/lemp-stack/actions/workflows/ci.yml)
[![Security Scan](https://github.com/Soumalya-De/lemp-stack/actions/workflows/security-scan-fixed.yml/badge.svg)](https://github.com/Soumalya-De/lemp-stack/actions/workflows/security-scan-fixed.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

---

## ✨ Features

- **🐳 Docker-Based**: Fully containerized with Docker Compose orchestration
- **🔒 Security-First**: Pre-commit secret scanning, vulnerability scans, SBOM generation
- **📊 Built-In Monitoring**: Uptime Kuma for real-time service health tracking
- **🚀 CI/CD Automated**: 5 GitHub Actions workflows for testing, scanning, and validation
- **💾 Backup/Restore**: Automated database backup and restore procedures
- **🔧 Development-Ready**: Hot-reload PHP files, Adminer for database management
- **📦 Supply Chain Security**: Dependabot auto-updates, image digest pinning
- **🎯 Production-Aligned**: Same codebase for dev/staging/production with env variables

---

## 🚀 Quick Start

Get up and running in 5 minutes:

```bash
# Clone the repository
git clone https://github.com/Soumalya-De/lemp-stack.git
cd lemp-stack

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

## 📚 Documentation

| Document | Description |
|----------|-------------|
| [**Architecture**](docs/architecture.md) | System design, components, network architecture, data flow |
| [**Quickstart Guide**](docs/quickstart.md) | Platform-specific setup, verification steps, first-time configuration |
| [**Configuration**](docs/configuration.md) | Environment variables, Docker Compose options, customization |
| [**CI/CD Workflows**](docs/ci-cd-workflows.md) | GitHub Actions pipelines, Dependabot, SBOM, security scanning |
| [**Security**](docs/security.md) | Security hardening, scanning workflows, secrets management |
| [**Monitoring**](docs/monitoring.md) | Uptime Kuma setup, alerting, observability best practices |
| [**Troubleshooting**](docs/troubleshooting.md) | Common issues, playbooks, debugging techniques |
| [**Performance Tuning**](docs/performance-tuning.md) | OPcache, Nginx, PHP-FPM optimization strategies |

---

## 🛠️ Technology Stack

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

## 📦 Project Structure

```
lemp-stack/
├── .github/
│   ├── workflows/          # CI/CD pipelines
│   └── dependabot.yml      # Automated dependency updates
├── docs/                   # Comprehensive documentation
├── mysql/
│   └── init.sql            # Database schema
├── nginx/
│   ├── Dockerfile          # Custom Nginx image
│   └── default.conf        # Virtual host configuration
├── php/
│   ├── Dockerfile          # Custom PHP-FPM image
│   └── php.ini             # Runtime configuration
├── scripts/
│   └── bcrypt.php          # Password hashing utility
├── www/                    # Application files
│   ├── index.php           # Homepage
│   ├── info.php            # PHP diagnostics (dev only)
│   └── test-db.php         # Database test (dev only)
├── docker-compose.yml      # Service orchestration
├── Makefile                # Common task automation
└── .env.example            # Environment template
```

---

## 🔐 Security

This project implements multiple layers of security:

- **Pre-Commit Scanning**: Gitleaks and detect-secrets prevent credential leaks
- **Vulnerability Scanning**: Trivy scans Docker images for known CVEs
- **SBOM Generation**: Complete software bill of materials for supply chain audits
- **Nightly Security Scans**: Automated vulnerability detection with GitHub Issues
- **Network Isolation**: Internal services not exposed to host
- **Secrets Management**: All credentials in `.env` (gitignored)

See [**Security Guide**](docs/security.md) for comprehensive security practices.

---

## 🤝 Contributing

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

## 📄 License

This project is licensed under the MIT License - see [LICENSE](LICENSE) for details.

Documentation is licensed under CC-BY-4.0 - see [LICENSE-DOCS](LICENSE-DOCS).

---

## 🙏 Acknowledgments

- [Docker](https://www.docker.com/) - Container platform
- [Uptime Kuma](https://github.com/louislam/uptime-kuma) - Monitoring solution
- [Trivy](https://github.com/aquasecurity/trivy) - Vulnerability scanner
- [Gitleaks](https://github.com/gitleaks/gitleaks) - Secret scanner

---

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/Soumalya-De/lemp-stack/issues)
- **Discussions**: [GitHub Discussions](https://github.com/Soumalya-De/lemp-stack/discussions)
- **Security**: See [SECURITY.md](SECURITY.md)

---

<p align="center">
  Made with ❤️ by <a href="https://github.com/Soumalya-De">Soumalya De</a>
</p>
