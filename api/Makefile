setup:
	@make build
	@make up 
	@make composer-update

build:
	docker-compose -f ./deployment/docker-compose.yml build --no-cache --force-rm

stop:
	docker-compose -f ./deployment/docker-compose.yml stop

up:
	docker-compose -f ./deployment/docker-compose.yml up -d

composer-update:
	docker exec Rentella_app bash -c "composer update"

data: 
	docker exec Rentella_app bash -c "php artisan migrate"
	docker exec Rentella_app bash -c "php artisan db:seed"

squeeze:xs
	php artisan schema:dump
	php artisan schema:dump --prune

rollback:
	php artisan migrate:rollback

new_migration:	
	$(eval MIGRATION_NAME := $(filter-out $@,$(MAKECMDGOALS)))
	docker exec Rentella_app bash -c "php artisan make:migration $(MIGRATION_NAME)"
