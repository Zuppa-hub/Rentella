# Rentella

![Rentella Logo](web/src/assets/LogoLight.svg)

Rentella is a beach umbrella rental platform. Think Airbnb for umbrellas: beach owners list availability, and users search by location and book.

![Version](https://img.shields.io/badge/version-1.2.0-blue)
![Status](https://img.shields.io/badge/status-In%20Development-yellow)
![License](https://img.shields.io/badge/license-MIT-green)

## About the project

Rentella is a full-stack application built to demonstrate real-world backend architecture, security practices, and system design—not just to ship features.

The focus: secure REST API with role-based access control, OAuth2 authentication (Keycloak), ownership validation on every write operation, comprehensive security tests, and a production-like Docker environment.

For developers: this is a reference project for secure API design and full-stack integration.

## Highlights

- Role-based access: admin, owners, users
- Secure API with Keycloak (OAuth2)
- Fully dockerized stack
- Feature tests for core endpoints

## What's next

> Back office in progress: building the admin area so beach owners can add and manage their own beaches.

## Demo

https://github.com/user-attachments/assets/8dbfe115-81bd-4bcd-9bcb-92a2e1721738

## Design

🎨 **Figma Design**: [View on Figma](https://www.figma.com/design/yAW6er4mw2SGyWjvUOUgJG/Rentella?node-id=1542-19194&t=QtE33frejJYOoWbq-1)

## What this project demonstrates

- **OAuth2 / OpenID Connect**: Keycloak integration from scratch—token validation, realm config, local/prod separations
- **Security-first API design**: ownership checks, role-based access, request validation (Laravel Form Requests), 40+ security tests
- **Backend architecture**: RESTful endpoints, Laravel best practices (models, migrations, seeders), proper error handling
- **Frontend UX**: Vue 3 Composition API, TypeScript, form validation, Keycloak SSO flow, responsive design
- **DevOps mindset**: Docker Compose, local/prod parity, multi-service orchestration (PHP + Node + PostgreSQL + Keycloak)
- **Test-driven development**: Feature tests for all security rules, CI automation, zero-trust model
- **Git workflow**: Conventional commits, branching strategy, documented code review process

## Architecture

**How it works**: Users log in via Keycloak → get JWT token → API validates token + ownership rules → response

```
┌─────────────────────────────────────────────────────────┐
│ Frontend (Vue 3 @ localhost:5173)                       │
│ ├─ Keycloak JS SDK handles login                        │
│ └─ API calls include Authorization: Bearer {JWT}        │
└──────────────────│─────────────────────────────────────┘
                   │ HTTP/REST
┌──────────────────▼─────────────────────────────────────┐
│ API (Laravel @ localhost:9000)                          │
│ ├─ JWT validation via Keycloak Guard                    │
│ ├─ Ownership/role checks on every write                 │
│ └─ MySQL 8 for application state                        │
└──────────────────│─────────────────────────────────────┘
                   │
┌──────────────────▼─────────────────────────────────────┐
│ Keycloak (@ localhost:8080)                             │
│ ├─ OAuth2 token issuer                                  │
│ ├─ User registry (PostgreSQL backend)                   │
│ └─ Role management                                      │
└─────────────────────────────────────────────────────────┘
```

All containerized. One `make -C api setup` and you're running a production-like local environment.

## Tech stack

- **Backend**: Laravel 10, PHP 8.1, MySQL 8
- **Frontend**: Vue 3, TypeScript, Vite
- **Auth**: Keycloak 26.1 (with PostgreSQL)
- **Infra**: Docker, Nginx

## Security notice

- All credentials in `.env.example` are **placeholders only** (`change_me_*`, empty strings, or `null`).
- Real `.env` files are gitignored and never committed.
- Local development uses Keycloak HTTPS policy set to `none` for convenience; production should enforce TLS via reverse proxy.
- See [SECURITY.md](SECURITY.md) for deployment hardening checklist.

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

This initializes containers, dependencies, migrations, and seed data.

If you previously ran an older local setup, reset containers and volumes before `setup` to avoid stale Keycloak/MySQL state:

```bash
docker-compose -f api/deployment/docker-compose.yml down -v --remove-orphans
```

### URLs

- Frontend: http://localhost:5173
- API: http://localhost:9000/api
- Keycloak: http://localhost:8080

## Troubleshooting

- **Keycloak HTTPS error**: recreate containers with `docker-compose -f api/deployment/docker-compose.yml down -v` and re-run setup.
- **Port 5173 not reachable**: check `docker-compose -f api/deployment/docker-compose.yml ps` to verify `vue` is running.

See [DOCS.md](DOCS.md) for full API reference and commands.

## CI / Tests

GitHub Actions runs automatically on every push:
- **Frontend**: `npm ci` + `npm run build`
- **API**: `composer install` + `php artisan migrate:fresh --force` + `php artisan test`

Run tests locally:
```bash
docker exec Rentella_app php artisan test tests/Feature/
```

See [SECURITY.md](SECURITY.md) for production hardening and deployment checklist.

## License

MIT

## Contact

Andrea Cazzato - cazzatoandrea@protonmail.com
