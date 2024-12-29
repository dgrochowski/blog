# Makefile

check-tools:
	@if ! command -v docker &> /dev/null; then \
		echo "Error: docker is not installed or not in PATH."; \
		exit 1; \
	fi
	@if ! command -v docker-compose &> /dev/null; then \
		echo "Error: docker-compose is not installed or not in PATH."; \
		exit 1; \
	fi

setup: check-tools
	cp git/hooks/pre-commit .git/hooks/pre-commit
	docker compose down
	docker compose pull
	docker compose build

build:
	docker compose up --build -d

start:
	docker compose up -d

migrate:
	docker compose exec -T php sh -c 'XDEBUG_MODE=off bin/console doctrine:migration:migrate -n'

check: start
	docker compose exec -T php sh -c 'XDEBUG_MODE=off vendor/bin/php-cs-fixer fix --diff'
	docker compose exec -T php sh -c 'XDEBUG_MODE=off symfony check:security'
	docker compose exec -T php sh -c 'XDEBUG_MODE=off vendor/bin/phpstan --no-interaction'
	docker compose exec -T php sh -c 'XDEBUG_MODE=off bin/phpunit'
	@echo
	@echo "██████╗ ██╗      ██████╗  ██████╗ "
	@echo "██╔══██╗██║     ██╔═══██╗██╔════╝ "
	@echo "██████╔╝██║     ██║   ██║██║  ███╗"
	@echo "██╔══██╗██║     ██║   ██║██║   ██║"
	@echo "██████╔╝███████╗╚██████╔╝╚██████╔╝"
	@echo "╚═════╝ ╚══════╝ ╚═════╝  ╚═════╝ "
	@echo

stop:
	docker compose down
