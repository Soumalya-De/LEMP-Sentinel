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
