# Rentella

Status: In progress

Rentella is an application for renting beach umbrellas. It uses a MySQL database with Laravel and Keycloak for user authentication and management, all containerized with Docker.

## Api usage

To get the list of all cities 
[Response in JSON, make request using GET method.]

```php
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
] 

```

To get the list of cities in a given range of latitudes and longitudes 
[Response in JSON, make request using GET method.]

```php
http://localhost:9000/public/api/cities/{{Min_latitude}}/{{Max_latitude}}/{{Min_longitude}}/{{Max_longitude}}
```

```php
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