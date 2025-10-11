SHELL := /bin/bash

.PHONY: build up up-nonroot down restart logs ps lint-php smoke reset env

build:
	docker compose build

up:
	docker compose up -d

up-nonroot:
	docker compose -f docker-compose.yml -f docker-compose.nonroot.yml up -d

down:
	docker compose down --volumes --remove-orphans

restart: down up

logs:
	docker compose logs -f --tail=200

ps:
	docker compose ps

lint-php:
	docker run --rm -v "$(PWD)/www":/app -w /app php:8.2-cli sh -lc 'set -e; if ls *.php >/dev/null 2>&1; then php -v; for f in $$(find . -name "*.php"); do php -l "$$f"; done; else echo "No PHP files to lint"; fi'

smoke:
	@echo "Checking /"
	@curl -sSf http://localhost:8080/ | head -n 2
	@echo "Checking /test-db.php"
	@curl -sSf http://localhost:8080/test-db.php
	@echo "Checking /info.php (should be 403 unless APP_ENV=development)"
	@bash -lc 'status=$$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/info.php); echo "info.php returned $$status"'

reset:
	docker compose down --volumes --remove-orphans

# --- Backup & Restore (example) ---
.PHONY: db-backup db-restore

db-backup:
	@echo "Dumping my_app_db to backups/my_app_db.sql"
	mkdir -p backups
	docker exec -i lemp_mysql sh -lc 'exec mysqldump -u "$${MYSQL_USER}" -p"$${MYSQL_PASSWORD}" "$${MYSQL_DATABASE}"' > backups/my_app_db.sql
	@echo "Backup written to backups/my_app_db.sql"

db-restore:
	@echo "Restoring my_app_db from backups/my_app_db.sql"
	@test -f backups/my_app_db.sql || (echo "Missing backups/my_app_db.sql" && exit 1)
	docker exec -i lemp_mysql sh -lc 'exec mysql -u "$${MYSQL_USER}" -p"$${MYSQL_PASSWORD}" "$${MYSQL_DATABASE}"' < backups/my_app_db.sql
	@echo "Restore completed"

# --- Utilities ---
.PHONY: bcrypt

# Generate a bcrypt hash using PHP (no local PHP required)
# Usage: make bcrypt PASSWORD=mysecret
bcrypt:
	@test -n "$(PASSWORD)" || (echo "Usage: make bcrypt PASSWORD=your_password" && exit 1)
	docker run --rm -v "$(PWD)/scripts":/scripts php:8.2-cli php /scripts/bcrypt.php "$(PASSWORD)"
