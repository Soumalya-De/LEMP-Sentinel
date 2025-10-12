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

See [Monitoring Guide](monitoring.md) for setup instructions.

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

## Related Documentation

- [Architecture](architecture.md) - System design and components
- [Quickstart](quickstart.md) - Getting started guide
- [Security](security.md) - Security hardening
- [Monitoring](monitoring.md) - Uptime Kuma setup
- [Performance Tuning](performance-tuning.md) - Optimization strategies
