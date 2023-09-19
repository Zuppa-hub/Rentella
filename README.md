# ReadMe

Date: August 28, 2023 → September 1, 2023
Status: In progress

# Rentella Backend.

Status: In progress

Rentella is an application for renting beach umbrellas. It uses a MySQL database with Laravel and Keycloak for user authentication and management, all containerized with Docker.

## Api usage

All the api use CRUD scheme. 

api are divided into 11 groups, as many as there are database tables.
The groups are: users, locations, beaches, beach-pictures, beach-types, beach-zones, opening-dates, orders, owners, prices, umbrellas. 

**Complete api documentation can be found here:** 

[Rentella api documentation](https://dark-zodiac-42041.postman.co/workspace/Team-Workspace~b9d9a449-2f75-41f1-8006-bb193b2a670e/collection/26738901-771c9524-b2e0-48dd-b2ce-17d5a7aebbcc?action=share&source=copy-link&creator=26738901)

## Compilation Instructions

1. Clone the Rentella repository from GitHub.
2. Open the terminal and navigate to the project folder.
3. Run the command `make setup` to create and start the Docker containers.
4. Access the application at `http://localhost:9001` from your web browser to see phpmyadmin, `http://localhost:8000`to see Keycloak. 

Note that the project is still in development and the code is subject to change.

## How to use

Short overview of make command 

```bash
make setup 
```

Allow user to setup container and environment, then start all the services. 
This command call `make build`, `make up` and `make composer-update` 

 

```bash
make build 
```

It is the short for `docker-compose -f ./deployment/docker-compose.yml build --no-cache --force-rm.` 
It build all the container from the docker compose file stored deployment subfolder. 

```bash
make up 
```

It is the short for `docker-compose -f ./deployment/docker-compose.yml up -d`. 
It start all the container present in the docker compose file in background, so you can still use your terminal console.

```bash
make composer-update
```

It is the short for `docker exec Rentella_app bash -c "../composer update”`. 
It allow user to build the composer file. 

```bash
make data 
```

 It is the short for `docker exec Rentella_app bash -c "php artisan migrate”` and `docker exec Rentella_app bash -c "php artisan db:seed”`.

It allow user to migrate database and seed the database. 

```bash
make rollback
```

It is the short for `php artisan migrate:rollback` 

It allow user to rollback db from the last migration file. 

```bash
make new_migration {{migration_name}}
```

It is the short for 

```bash
$(eval MIGRATION_NAME := $(filter-out $@,$(MAKECMDGOALS)))
	docker exec Rentella_app bash -c "php artisan make:migration $(MIGRATION_NAME)"
```

It accept argoument as migration name and create a new migration.