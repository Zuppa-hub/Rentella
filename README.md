# Rentella

![Rentella Logo](web/src/assets/LogoLight.svg)

Rentella is a beach umbrella rental platform. Think Airbnb for umbrellas: beach owners list availability, users search by location and book.

![Version](https://img.shields.io/badge/version-1.2.0-blue)
![Status](https://img.shields.io/badge/status-In%20Development-yellow)
![License](https://img.shields.io/badge/license-MIT-green)

- Role-based access: admin, owners, users
- Secure API with Keycloak (OAuth2)
- Fully dockerized stack
- Feature tests for core endpoints

## Design

🎨 **Figma Design**: [View on Figma](https://www.figma.com/design/yAW6er4mw2SGyWjvUOUgJG/Rentella?node-id=1542-19194&t=QtE33frejJYOoWbq-1)

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

## CI quality gates

GitHub Actions runs automatically:

- `web`: `npm ci` + `npm run build`
- `api`: `composer install` + `php artisan migrate:fresh --force` + `php artisan test`

Workflow file: `.github/workflows/ci.yml`

## Production hardening checklist

Before deploying to production:

- set strong credentials in `api/.env` for `MYSQL_ROOT_PASSWORD`, `POSTGRES_PASSWORD`, `KEYCLOAK_ADMIN_PASSWORD`
- set `APP_ENV=production` and `APP_DEBUG=false`
- set `LOG_LEVEL=warning` or `error`
- restrict `CORS_ALLOWED_ORIGINS` to authorized public domains only
- avoid default admin accounts (`KEYCLOAK_ADMIN=admin`) and use a dedicated name

## License

MIT

## Contact

Andrea Cazzato - cazzatoandrea@protonmail.com
