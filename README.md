# Rentella Backend.

Status: In progress

Rentella is an application for renting beach umbrellas. It uses a MySQL database with Laravel and Keycloak for user authentication and management, all containerized with Docker.

## Api usage

**First you must be authenticated**

For getting JWT token: 
[Response in JSON, make request using POST method.]

```json
localhost:8080/realms/Rentella/protocol/openid-connect/token
```

| Key  | Value |
| --- | --- |
| client_id | api |
| grant_type | password |
| client_secret | {{client_secret}} |
| username | {{username}} |
| password | {{password}} |
| Realm | Rentella |
- Email of the user will be checked if is in the users table of the db.

Response if all key is correct will be a JWT token.

Use the JWT token as bearer token in the header of the api request 

| Key | Value |
| --- | --- |
| Authorization | Bearer
{{Token}} |

**To get the list of all cities** 

[Response in JSON, make request using GET method.]

```bash
http://localhost:9000/public/api/city-locations
```

Response: 

```json
[
    {
        "id": 1,
        "city_name": "North Medaville",
        "latitude": -50.49,
        "longitude": -165.36,
        "description": "Sint aut est possimus cumque a.",
        "created_at": "2023-09-05T09:21:47.000000Z",
        "updated_at": "2023-09-05T09:21:47.000000Z"
    },
    {
        "id": 2,
        "city_name": "Romaguerafort",
        "latitude": 66.86,
        "longitude": 148.33,
        "description": "Et expedita libero voluptas rerum dolores accusantium.",
        "created_at": "2023-09-05T09:21:47.000000Z",
        "updated_at": "2023-09-05T09:21:47.000000Z"
    },
```

To get the list of cities in a given range of latitudes and longitudes 
[Response in JSON, make request using GET method.]

```bash
http://localhost:9000/public/api/cities/{{Min_latitude}}/{{Max_latitude}}/{{Min_longitude}}/{{Max_longitude}}
```

```bash
http://localhost:9000/public/api/cities/40.0/70.0/140.0/180.0
```

Response 

```json
[
    {
        "id": 2,
        "city_name": "Romaguerafort",
        "latitude": 66.86,
        "longitude": 148.33,
        "description": "Et expedita libero voluptas rerum dolores accusantium.",
        "created_at": "2023-09-05T09:21:47.000000Z",
        "updated_at": "2023-09-05T09:21:47.000000Z"
    }
]
```

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