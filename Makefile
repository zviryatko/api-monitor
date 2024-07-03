include .env

.PHONY: tmp up down start stop install prune ps build drush cli logs test

# Check required arguments.
ifndef ENV
	ENV = development
endif

default: up install

DRUPAL_ROOT ?= /var/www/html/docroot

up:
	@echo "Starting up containers for $(PROJECT_NAME)..."
	docker compose pull --quiet
	docker compose up --quiet-pull -d --remove-orphans $(filter-out $@,$(MAKECMDGOALS))

down: stop

start:
	@echo "Starting containers for $(PROJECT_NAME)..."
	@docker compose start

stop:
	@echo "Stopping containers for $(PROJECT_NAME)..."
	@docker compose stop

install:
	@echo "Installing $(PROJECT_NAME)..."
	@docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app composer install
	@docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app ash -ic 'drush site-install --existing-config --account-name=$${DRUPAL_USER} --account-pass=$${DRUPAL_PASS} -y --db-url=mysql://$${DB_USER}:$${DB_PASSWORD}@$${DB_HOST}/$${DB_NAME}'
	@docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app drush en default_content
	@docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app drush eval '\Drupal::service("default_content.importer")->importContentFromFolder("../tests/content");'
	@docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app drush pmu default_content
	@docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app drush sapi-c --yes
	@docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app drush sapi-i --yes

prune:
	@echo "Removing containers for $(PROJECT_NAME)..."
	@docker compose down -v

ps:
	@docker compose ps

drush:
	docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app drush $(filter-out $@,$(MAKECMDGOALS))

cli:
	docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app ash

logs:
	@docker compose logs -f $(filter-out $@,$(MAKECMDGOALS))

test:
	@docker compose exec --user "${WWW_DATA_UID}:${WWW_DATA_GID}" app phpunit

# https://stackoverflow.com/a/6273809/1826109
%:
	@:
