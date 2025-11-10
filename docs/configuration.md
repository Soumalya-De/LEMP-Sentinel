# Configuration Reference

Complete guide to environment variables, Docker Compose options, and customization.

## Environment Variables (.env)

### Database Configuration

```bash
# MySQL root password (admin access)
MYSQL_ROOT_PASSWORD=rootpass

# Application database name
MYSQL_DATABASE=lemp_db

# Application database user
MYSQL_USER=lemp_user

# Application database password
MYSQL_PASSWORD=lemp_password
```

**Security Notes**:
- Use strong passwords in production (20+ characters, mixed case, symbols)
- Never commit `.env` to version control
- Rotate credentials regularly (quarterly recommended)

### Application Environment

```bash
# Environment mode: development or production
APP_ENV=development
```

**Effects**:

| Feature | Development | Production |
|---------|-------------|------------|
| `info.php` (phpinfo) | ✅ Enabled | ❌ Returns 404 |
| `test-db.php` | ✅ Enabled | ❌ Returns 404 |
| Adminer | ✅ Started | ❌ Not started |
| PHP error reporting | Full details | Minimal logs |

### Base Image Configuration

```bash
# Override Nginx base image
NGINX_BASE_IMAGE=nginx:alpine

# Override PHP base image
PHP_BASE_IMAGE=php:8.2-fpm-alpine
```

**Use Cases**:
- Pin to specific versions: `nginx:alpine3.20`
- Use digest-pinned images: `nginx@sha256:abc123...`
- Test patched images: `ghcr.io/org/nginx-patched:v1.0`

### Monitoring Configuration (Optional)

```bash
# Telegram Bot Token (from @BotFather)
TELEGRAM_BOT_TOKEN=1234567890:ABCdefGHIjklMNOpqrsTUVwxyz

# Telegram Chat ID (from @userinfobot or group info)
TELEGRAM_CHAT_ID=-1001234567890

# Discord Webhook URL
DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/...

# SMTP for email alerts
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=alerts@example.com
SMTP_PASSWORD=app_password
SMTP_FROM=noreply@example.com
SMTP_TO=oncall@example.com
```

Configure Uptime Kuma monitoring via the web interface at http://localhost:3001.

## Docker Compose Options

### Default Configuration (docker-compose.yml)

Standard setup with root PHP-FPM process:

```bash
docker compose up -d
```

Services:
- `nginx`: Reverse proxy
- `php`: PHP-FPM runtime
- `mysql`: Database
- `uptime-kuma`: Monitoring
- `adminer`: Database UI (optional with `--profile dev`)

### Non-Root Configuration (docker-compose.nonroot.yml)

Run PHP-FPM as unprivileged user for enhanced security:

```bash
docker compose -f docker-compose.yml -f docker-compose.nonroot.yml up -d
```

**Changes**:
- PHP-FPM runs as user `www-data` (UID 82, GID 82)
- Files in `www/` must be readable by UID 82
- Fixes Linux permission issues with mounted volumes

**When to Use**: Production deployments, security audits, compliance requirements.

### Production Configuration (docker-compose.prod.yml)

Use digest-pinned images for supply chain security:

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

**Changes**:
- Images referenced by SHA256 digest (immutable)
- Prevents tag manipulation attacks
- Ensures reproducible builds

**Example**:
```yaml
services:
  nginx:
    image: nginx@sha256:1234567890abcdef...  # Pinned digest
```

Update digests using [Build PHP Patched workflow](ci-cd-workflows.md#build-php-patched-workflow).

### Development Profile

Start with Adminer for database management:

```bash
docker compose --profile dev up -d
```

or use Makefile:

```bash
make up-dev
```

**Adminer Access**: http://localhost:8081

### Combining Configurations

Stack multiple compose files for advanced scenarios:

```bash
# Production + Non-root
docker compose -f docker-compose.yml \
  -f docker-compose.prod.yml \
  -f docker-compose.nonroot.yml \
  up -d
```

## Service Configuration Files

### Nginx (nginx/default.conf)

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

**Key Settings**:
- `fastcgi_pass php:9000`: Forward PHP requests to PHP-FPM container
- `root /var/www/html`: Document root (maps to `www/` directory)
- `try_files`: Support for URL rewriting (frameworks like Laravel)

**Customization**:
- Add HTTPS listener (requires certificates)
- Configure caching headers
- Enable gzip compression
- Add security headers (CSP, HSTS, X-Frame-Options)

### PHP (php/php.ini)

```ini
# Memory and execution
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 64M
post_max_size = 64M

# OPcache
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1

# Error reporting (development)
display_errors = On
error_reporting = E_ALL
```

**Key Settings**:
- `memory_limit`: Max memory per script (increase for large operations)
- `upload_max_filesize`: Max file upload size
- `opcache.enable`: Enable bytecode caching (10x performance boost)
- `opcache.revalidate_freq`: Check for file changes every N seconds

**Production Changes**:
```ini
display_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
opcache.revalidate_freq = 60  # Check files less frequently
```

### MySQL (mysql/init.sql)

Initialization script runs once on first startup:

```sql
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add your tables here
```

**Important**: This runs ONLY on first startup. To apply changes:
1. Edit `mysql/init.sql`
2. Remove existing volume: `docker compose down --volumes`
3. Restart: `docker compose up -d`

**Warning**: `--volumes` deletes all data. Backup first if data exists.

## Health Check Configuration

### Nginx Health Check

```yaml
healthcheck:
  test: ["CMD", "curl", "-f", "http://localhost"]
  interval: 10s
  timeout: 5s
  retries: 3
  start_period: 20s
```

**What It Checks**: Nginx responds to HTTP requests on port 80.

### PHP-FPM Health Check

```yaml
healthcheck:
  test: ["CMD-SHELL", "php-fpm -t || exit 1"]
  interval: 10s
  timeout: 5s
  retries: 3
  start_period: 30s
```

**What It Checks**: PHP-FPM configuration is valid and process is running.

### MySQL Health Check

```yaml
healthcheck:
  test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
  interval: 10s
  timeout: 5s
  retries: 3
  start_period: 30s
```

**What It Checks**: MySQL accepts connections and processes queries.

## Port Configuration

Default ports can be changed in `docker-compose.yml`:

```yaml
services:
  nginx:
    ports:
      - "8080:80"  # Change 8080 to desired host port

  adminer:
    ports:
      - "8081:8080"  # Change 8081 to desired host port

  uptime-kuma:
    ports:
      - "3001:3001"  # Change 3001 to desired host port
```

**Format**: `"<host_port>:<container_port>"`

## Volume Configuration

### Named Volumes

```yaml
volumes:
  mysql_data:
    driver: local
  kuma_data:
    driver: local
```

**Location**: Managed by Docker (typically `/var/lib/docker/volumes/`)

**Backup**:
```bash
# MySQL
docker run --rm \
  -v lemp-stack_mysql_data:/data \
  -v $(pwd):/backup \
  alpine tar czf /backup/mysql-backup.tar.gz /data

# Uptime Kuma
docker run --rm \
  -v lemp-stack_kuma_data:/data \
  -v $(pwd):/backup \
  alpine tar czf /backup/kuma-backup.tar.gz /data
```

### Bind Mounts

Application files use bind mounts for hot-reload:

```yaml
services:
  php:
    volumes:
      - ./www:/var/www/html  # Live reload PHP files
```

**Changes reflect immediately** - no container restart needed.

## Network Configuration

### Internal Network

```yaml
networks:
  lemp_network:
    driver: bridge
```

**Isolation**: Services communicate via Docker DNS:
- `http://nginx` (not `http://localhost`)
- `mysql://mysql:3306` (not `mysql://localhost:3306`)

**Security**: PHP-FPM and MySQL NOT exposed to host network.

### External Network (Advanced)

Connect to external networks:

```yaml
networks:
  lemp_network:
    driver: bridge
  external_network:
    external: true
    name: traefik_network
```

**Use Case**: Integration with reverse proxies (Traefik, nginx-proxy).

## Environment-Specific Configuration

### Development

```bash
# .env
APP_ENV=development
MYSQL_ROOT_PASSWORD=dev_root_pass
MYSQL_PASSWORD=dev_pass

# Start with dev profile
docker compose --profile dev up -d
```

### Staging

```bash
# .env.staging
APP_ENV=production
MYSQL_ROOT_PASSWORD=<strong_password>
MYSQL_PASSWORD=<strong_password>

# Start with staging overrides
docker compose --env-file .env.staging up -d
```

### Production

```bash
# .env.production
APP_ENV=production
MYSQL_ROOT_PASSWORD=<vault_reference>
MYSQL_PASSWORD=<vault_reference>

# Start with digest-pinned images
docker compose -f docker-compose.yml \
  -f docker-compose.prod.yml \
  --env-file .env.production \
  up -d
```

## Makefile Variables

Override Makefile defaults:

```bash
# Specify backup file for restore
make restore BACKUP_FILE=backups/backup-20240115.sql

# View logs for specific service
make logs SERVICE=nginx

# Generate bcrypt hash
make bcrypt PASSWORD=mysecret
```

## Advanced Configuration

### PHP Extensions

Add extensions in `php/Dockerfile`:

```dockerfile
RUN docker-php-ext-install \
    mysqli \
    pdo_mysql \
    opcache \
    bcmath \
    gd \         # Add image processing
    zip \        # Add zip support
    intl         # Add internationalization
```

**After Changes**: Rebuild image with `docker compose build php`

### Nginx Modules

Add modules in `nginx/Dockerfile`:

```dockerfile
FROM nginx:alpine

# Install additional modules
RUN apk add --no-cache \
    nginx-mod-http-headers-more \
    nginx-mod-http-geoip

COPY default.conf /etc/nginx/conf.d/default.conf
```

### MySQL Configuration

Create `mysql/my.cnf`:

```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 1G
query_cache_type = 1
query_cache_size = 64M
```

Mount in `docker-compose.yml`:

```yaml
services:
  mysql:
    volumes:
      - ./mysql/my.cnf:/etc/mysql/conf.d/custom.cnf
```

## Troubleshooting Configuration

### Verify Environment Variables

```bash
# Check loaded environment
docker compose config

# Inspect specific service
docker compose config nginx
```

### Test Configuration Files

```bash
# Test Nginx config
docker compose exec nginx nginx -t

# Test PHP config
docker compose exec php php --ini
```

### Debug Health Checks

```bash
# Inspect service health
docker compose ps

# View health check logs
docker inspect lemp-stack-nginx | jq '.[0].State.Health'
```

---

## Production Deployment Considerations

### What's Included Out of the Box

✅ **Security Hardening**
- Nginx security headers (CSP, X-Frame-Options, HSTS)
- Rate limiting (60 requests/minute per IP)
- Pre-commit hooks for secrets scanning (Gitleaks, detect-secrets)
- Container hardening (non-root users, capability dropping, read-only filesystems)

✅ **Automated Vulnerability Management**
- Trivy security scans (weekly schedule)
- Automated CVE issue creation with SLAs
- SBOM generation for supply chain visibility
- CVE remediation playbook with defined workflows

✅ **Health Monitoring**
- Uptime Kuma for service monitoring
- Docker health checks for all services
- Telegram alert integration
- Restart policies for automatic recovery

✅ **Comprehensive Documentation**
- 7 technical guides covering all aspects
- Architecture diagrams and design decisions
- Configuration reference (this document)
- Security hardening best practices

✅ **CI/CD Workflows**
- 6 GitHub Actions workflows
- Automated security scanning
- Backup validation
- Dependency updates via Dependabot

---

### What You'll Need to Add for Production

#### 1. TLS/HTTPS Configuration

**Current State:** HTTP only (port 80)  
**Production Requirement:** HTTPS with valid certificates

**Option A: Traefik with Let's Encrypt (Recommended for Docker)**

```yaml
# docker-compose.traefik.yml
services:
  traefik:
    image: traefik:v2.10
    command:
      - "--providers.docker=true"
      - "--providers.docker.exposedbydefault=false"
      - "--entrypoints.web.address=:80"
      - "--entrypoints.websecure.address=:443"
      - "--certificatesresolvers.letsencrypt.acme.tlschallenge=true"
      - "--certificatesresolvers.letsencrypt.acme.email=ops@example.com"
      - "--certificatesresolvers.letsencrypt.acme.storage=/letsencrypt/acme.json"
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock:ro
      - ./letsencrypt:/letsencrypt
    labels:
      - "traefik.enable=true"

  nginx:
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.nginx.rule=Host(`example.com`)"
      - "traefik.http.routers.nginx.entrypoints=websecure"
      - "traefik.http.routers.nginx.tls.certresolver=letsencrypt"
```

**Benefits:**
- Automatic certificate issuance and renewal
- No manual certificate management
- Supports multiple domains

**Option B: Cloud Load Balancer**

Use managed load balancers with built-in certificate management:
- **AWS:** Application Load Balancer (ALB) + AWS Certificate Manager (ACM)
- **GCP:** HTTPS Load Balancer + Google-managed certificates
- **Azure:** Application Gateway + Azure Key Vault certificates

**Benefits:**
- Fully managed by cloud provider
- No need to expose certificate storage
- Integrated with cloud DNS

**Option C: Nginx with Certbot**

```bash
# Install certbot
apt-get install certbot python3-certbot-nginx

# Obtain certificate
certbot --nginx -d example.com -d www.example.com

# Auto-renewal (crontab)
0 0 * * * certbot renew --quiet
```

Add to `nginx/default.conf`:
```nginx
server {
    listen 443 ssl http2;
    server_name example.com;
    
    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # ... rest of config
}

# HTTP to HTTPS redirect
server {
    listen 80;
    server_name example.com;
    return 301 https://$server_name$request_uri;
}
```

**Why Not Included:**
- TLS setup is environment-specific (domain, DNS provider, certificate authority)
- Requires external domain and DNS configuration
- Different solutions for different deployment targets

---

#### 2. Secrets Management

**Current State:** `.env` files (gitignored, suitable for dev/staging)  
**Production Requirement:** Centralized secret vault with access controls

**Option A: AWS Secrets Manager with OIDC**

```yaml
# .github/workflows/deploy.yml
permissions:
  id-token: write  # Required for OIDC
  contents: read

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Configure AWS credentials
        uses: aws-actions/configure-aws-credentials@v2
        with:
          role-to-assume: arn:aws:iam::ACCOUNT_ID:role/GitHubActionsRole
          aws-region: us-east-1
      
      - name: Retrieve secrets
        run: |
          aws secretsmanager get-secret-value \
            --secret-id lemp/production \
            --query SecretString \
            --output text > .env
```

**IAM Policy for OIDC:**
```json
{
  "Version": "2012-10-17",
  "Statement": [{
    "Effect": "Allow",
    "Principal": {
      "Federated": "arn:aws:iam::ACCOUNT_ID:oidc-provider/token.actions.githubusercontent.com"
    },
    "Action": "sts:AssumeRoleWithWebIdentity",
    "Condition": {
      "StringEquals": {
        "token.actions.githubusercontent.com:sub": "repo:Soumalya-De/LEMP-Sentinel:ref:refs/heads/main"
      }
    }
  }]
}
```

**Benefits:**
- No long-lived credentials in GitHub
- Audit logging via CloudTrail
- Automatic secret rotation
- Fine-grained access control

**Option B: HashiCorp Vault**

```bash
# Store secret
vault kv put secret/lemp/production \
  MYSQL_ROOT_PASSWORD="..." \
  MYSQL_PASSWORD="..."

# Retrieve in CI/CD
vault kv get -field=MYSQL_ROOT_PASSWORD secret/lemp/production
```

**Option C: Azure Key Vault, GCP Secret Manager**

Similar patterns with cloud-specific tooling.

**Why Not Included:**
- Requires cloud provider account and IAM setup
- Adds operational complexity for local development
- Costs associated with secret storage (though minimal)

**Migration Path:**
1. Set up vault in cloud provider
2. Migrate secrets from `.env` to vault
3. Update deployment scripts to fetch from vault
4. Remove `.env` from production servers
5. Implement secret rotation policy (90-day recommended)

---

#### 3. Centralized Logging & Metrics

**Current State:** Docker logs + Uptime Kuma health checks  
**Production Requirement:** Aggregated logs, metrics dashboards, alerting

**Option A: Prometheus + Grafana + Loki (Self-Hosted)**

```yaml
# docker-compose.observability.yml
services:
  prometheus:
    image: prom/prometheus:latest
    volumes:
      - ./monitoring/prometheus.yml:/etc/prometheus/prometheus.yml
      - prometheus_data:/prometheus
    ports:
      - "9090:9090"
  
  grafana:
    image: grafana/grafana:latest
    volumes:
      - grafana_data:/var/lib/grafana
      - ./monitoring/dashboards:/etc/grafana/provisioning/dashboards
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=${GRAFANA_PASSWORD}
  
  loki:
    image: grafana/loki:latest
    ports:
      - "3100:3100"
    volumes:
      - loki_data:/loki
  
  promtail:
    image: grafana/promtail:latest
    volumes:
      - /var/log:/var/log:ro
      - /var/lib/docker/containers:/var/lib/docker/containers:ro
      - ./monitoring/promtail-config.yml:/etc/promtail/config.yml
```

**Exporters to Add:**
- PHP-FPM Exporter: Monitor PHP processes, request rates
- Nginx Exporter: Track request metrics, response times
- MySQL Exporter: Database connections, query performance
- Node Exporter: System metrics (CPU, memory, disk)

**Option B: Cloud-Native Solutions**

- **AWS:** CloudWatch Logs + CloudWatch Metrics + CloudWatch Dashboards
- **GCP:** Cloud Logging + Cloud Monitoring
- **Azure:** Azure Monitor + Log Analytics
- **Datadog:** Unified logging, metrics, and APM
- **New Relic:** Full-stack observability

**Why Not Included:**
- Observability requirements vary by scale (small deployments vs. multi-region)
- Adds infrastructure complexity and cost
- Requires expertise to configure dashboards and alerts properly
- This project focuses on security automation, not observability platform

**What to Monitor (When You Add This):**
- **Logs:** Application errors, security events, access logs
- **Metrics:** Request rates, response times, error rates, resource utilization
- **Alerts:** Service downtime, high error rates, security anomalies
- **Dashboards:** Real-time visibility into system health

---

#### 4. Rollback & Deployment Safety

**Current State:** Docker Compose with digest-pinned images  
**Production Requirement:** Tested rollback procedures, deployment automation

**Image Tagging Strategy:**

```bash
# Semantic versioning for releases
docker tag php:latest php:v1.2.0
docker tag php:latest php:v1.2
docker tag php:latest php:v1

# SHA-based tags for commits
docker tag php:latest php:sha-abc123f

# Production uses digest-pinned images
services:
  php:
    image: ghcr.io/soumalya-de/lemp-php@sha256:abc123...
```

**Deployment Script Example:**

```bash
#!/bin/bash
# deploy.sh - Safe production deployment

set -euo pipefail

ENVIRONMENT=$1
VERSION=$2

echo "Deploying $VERSION to $ENVIRONMENT"

# 1. Pull new images
docker compose -f docker-compose.prod.yml pull

# 2. Run smoke tests against staging
./scripts/smoke-test.sh staging

# 3. Create backup
./scripts/backup-db.sh

# 4. Deploy new version
docker compose -f docker-compose.prod.yml up -d --no-deps web nginx php

# 5. Wait for health checks
sleep 10

# 6. Verify deployment
if ./scripts/health-check.sh; then
    echo "✅ Deployment successful"
    docker image prune -f  # Clean up old images
else
    echo "❌ Deployment failed, rolling back"
    docker compose -f docker-compose.prod.yml up -d --no-deps \
        --scale web=1 \
        php:$PREVIOUS_VERSION
    exit 1
fi
```

**Rollback Procedure:**

```bash
# Quick rollback to previous version
git log --oneline  # Identify last known good version
git checkout v1.1.0

# Update docker-compose.prod.yml to previous digests
services:
  php:
    image: ghcr.io/soumalya-de/lemp-php@sha256:PREVIOUS_DIGEST

# Deploy previous version
docker compose -f docker-compose.prod.yml up -d

# Verify
./scripts/health-check.sh
```

**Database Migration Safety:**

```bash
# migrations/001_example.sql
-- Migration: Add user_preferences table
-- Rollback: DROP TABLE user_preferences;

BEGIN;

CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    preferences JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Test migration
SELECT COUNT(*) FROM user_preferences;

COMMIT;
```

**Why Not Fully Automated:**
- Deployment targets vary (single server vs. Kubernetes vs. serverless)
- Rollback strategies depend on architecture
- Database migrations require careful planning
- Different teams have different CI/CD tooling

---

#### 5. Backup & Disaster Recovery

**Current State:** Manual backup script in `scripts/`  
**Production Requirement:** Automated backups, tested restore procedures

**Automated Backup Strategy:**

```yaml
# docker-compose.backup.yml
services:
  backup:
    image: alpine:latest
    volumes:
      - mysql_data:/data/mysql:ro
      - ./backups:/backups
    environment:
      - BACKUP_SCHEDULE=0 2 * * *  # 2 AM daily
      - RETENTION_DAYS=30
    command: |
      /bin/sh -c "
      while true; do
        DATE=$(date +%Y%m%d_%H%M%S)
        tar czf /backups/mysql_$DATE.tar.gz /data/mysql
        find /backups -name 'mysql_*.tar.gz' -mtime +$RETENTION_DAYS -delete
        sleep 86400
      done
      "
```

**Restore Testing:**

```bash
#!/bin/bash
# scripts/test-restore.sh

# 1. Stop services
docker compose down

# 2. Restore backup
tar xzf backups/mysql_20251109_020000.tar.gz -C /tmp/restore

# 3. Start with restored data
docker volume rm mysql_data
docker volume create mysql_data
docker run --rm \
    -v mysql_data:/target \
    -v /tmp/restore/data/mysql:/source \
    alpine sh -c "cp -a /source/. /target/"

# 4. Start services
docker compose up -d

# 5. Verify data integrity
docker compose exec mysql mysql -u root -p$MYSQL_ROOT_PASSWORD -e "SHOW DATABASES;"
```

**Offsite Backup (Production):**

```bash
# Upload to S3
aws s3 sync ./backups/ s3://lemp-backups/mysql/ \
    --storage-class STANDARD_IA \
    --lifecycle-policy backup-lifecycle.json

# Or use rsync to remote server
rsync -avz --delete ./backups/ backup-server:/backups/lemp/
```

---

### Production Deployment Checklist

Use this checklist before going live:

#### Pre-Deployment
- [ ] Configure TLS certificates (Let's Encrypt, cloud provider, or custom)
- [ ] Set up centralized secret management (AWS Secrets Manager, Vault, etc.)
- [ ] Implement centralized logging (Prometheus/Grafana or cloud solution)
- [ ] Test rollback procedure with staging environment
- [ ] Configure automated backups with offsite storage
- [ ] Set up monitoring alerts (PagerDuty, Opsgenie, or Telegram)
- [ ] Document runbooks for common incidents
- [ ] Review and update security headers for your domain
- [ ] Configure rate limiting based on expected traffic
- [ ] Test disaster recovery procedure

#### Security
- [ ] Rotate all default credentials
- [ ] Enable firewall rules (allow only 80/443, SSH from specific IPs)
- [ ] Configure fail2ban or equivalent intrusion prevention
- [ ] Set up security monitoring (Falco, OSSEC, or cloud native)
- [ ] Enable audit logging for all administrative actions
- [ ] Review and apply least privilege access controls
- [ ] Configure HTTPS redirect (HTTP → HTTPS)
- [ ] Test for common vulnerabilities (OWASP Top 10)

#### Performance
- [ ] Tune PHP-FPM worker processes based on server capacity
- [ ] Configure MySQL query cache and buffer pools
- [ ] Set up CDN for static assets (Cloudflare, AWS CloudFront)
- [ ] Enable Nginx gzip compression
- [ ] Configure browser caching headers
- [ ] Load test to determine capacity limits

#### Compliance & Documentation
- [ ] Document data retention policies
- [ ] Create incident response plan
- [ ] Set up change management process
- [ ] Configure compliance logging (if required: PCI-DSS, HIPAA, etc.)
- [ ] Update documentation with production specifics
- [ ] Train team on deployment and rollback procedures

---

### Why These Aren't Included by Default

**Philosophy:** This project provides a **production-ready starting point**, not a complete production environment.

**Reasons:**
1. **Environment Variability:** Production needs differ drastically
   - Small business: Single server, Let's Encrypt, CloudWatch
   - Enterprise: Kubernetes, Vault, Datadog, multi-region

2. **Operational Complexity:** Adding everything increases:
   - Learning curve for newcomers
   - Infrastructure costs
   - Maintenance burden

3. **Focus:** This project demonstrates **security automation**, not full observability platform

4. **Flexibility:** You choose tools that fit your stack:
   - AWS shop? Use AWS-native tools
   - Self-hosted? Use Prometheus/Grafana
   - Hybrid? Mix and match

**What You Get:**
- Secure foundation with hardened containers
- Automated vulnerability management
- Clear documentation of what's needed
- Patterns you can extend

**What You Add:**
- TLS for your specific domain
- Secrets management for your cloud
- Logging that fits your budget
- Deployment automation for your workflow

---

## Related Documentation

- [Architecture](architecture.md) - System design and components
- [Quickstart](quickstart.md) - Getting started guide
- [Security Hardening](security-hardening.md) - Detailed security guide
- [CVE Playbook](cve-playbook.md) - Vulnerability management procedures
- [CI/CD Workflows](ci-cd-workflows.md) - Automation documentation
