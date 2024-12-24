# Makefile

build:
	docker compose up --build -d

start:
	docker compose up -d

check:
	docker compose exec -T php sh -c 'XDEBUG_MODE=off vendor/bin/phpstan --no-interaction'

stop:
	docker compose down
