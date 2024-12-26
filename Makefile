# Makefile

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
