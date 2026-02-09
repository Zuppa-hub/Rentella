# Rentella

Rentella is a beach umbrella rental platform. Think Airbnb for umbrellas: beach owners list availability, users search by location and book.

![Version](https://img.shields.io/badge/version-0.1.0-blue)
![Status](https://img.shields.io/badge/status-In%20Development-yellow)
![License](https://img.shields.io/badge/license-MIT-green)

- Role-based access: admin, owners, users
- Secure API with Keycloak (OAuth2)
- Fully dockerized stack
- Feature tests for core endpoints

## Tech stack

- Backend: Laravel 10, PHP 8.1, MySQL
- Frontend: Vue 3, TypeScript, Vite
- Infra: Docker, Nginx, Keycloak

## Quick start

### Prerequisites

- Docker and Docker Compose
- Git

### Setup

```bash
git clone https://github.com/Zuppa-hub/Rentella.git
cd Rentella

cp api/.env.example api/.env
cp web/.env.example web/.env

make -C api setup
make -C api data
```

### URLs

- Frontend: http://localhost:5173
- API: http://localhost:9000/api
- Keycloak: http://localhost:8080

## API overview

Authentication required:
- `/api/users`
- `/api/beaches`
- `/api/beach-pictures`
- `/api/beach-zones`
- `/api/umbrellas`
- `/api/prices`
- `/api/opening-dates`
- `/api/orders`

Admin only:
- `/api/owners`
- `/api/locations`
- `/api/beach-types`

Public:
- `POST /api/users`
- `GET /api/health`

## Useful commands

```bash
make -C api up
make -C api stop
make -C api data
make -C api rollback
make -C api new_migration name_here
```

## Tests

```bash
docker exec Rentella_app php artisan test tests/Feature/
```

## License

MIT

## Contact

Andrea Cazzato - cazzatoandrea@protonmail.com
