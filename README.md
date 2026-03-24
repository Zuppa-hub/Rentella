# Rentella

![Rentella Logo](web/src/assets/LogoLight.svg)

Rentella is a beach umbrella rental platform built to demonstrate real-world backend architecture, security practices, and system design. A full-stack reference project: secure REST API with OAuth2 (Keycloak), role-based access, ownership validation on every write, comprehensive security tests, and a production-like Docker environment.

![Version](https://img.shields.io/badge/version-1.2.0-blue)
![Status](https://img.shields.io/badge/status-In%20Development-yellow)
![License](https://img.shields.io/badge/license-MIT-green)

## Demo

https://github.com/user-attachments/assets/8dbfe115-81bd-4bcd-9bcb-92a2e1721738

## Design

🎨 **Figma Design**: [View on Figma](https://www.figma.com/design/yAW6er4mw2SGyWjvUOUgJG/Rentella?node-id=1542-19194&t=QtE33frejJYOoWbq-1)

## What this project demonstrates

- **OAuth2 / OpenID Connect**: Keycloak integration—token validation, realm configuration, local/prod separation
- **Security-first API design**: ownership checks, role-based access, request validation, 40+ security tests
- **DevOps mindset**: Docker Compose with 8 containerized services, local/prod parity, multi-service orchestration
- **Full-stack architecture**: RESTful API (Laravel), Vue 3 frontend with TypeScript, Keycloak auth integration

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
- **401/403 on protected API endpoints in strict mode**: with `KEYCLOAK_IGNORE_RESOURCES_VALIDATION=false` and DB-backed users enabled, tokens must include `resource_access` for `rentella-api` and the authenticated user must exist in the local `users` table.

### Strict mode quick alignment

If you keep strict defaults (`KEYCLOAK_IGNORE_RESOURCES_VALIDATION=false` and DB-backed users enabled), align your local auth flow with these commands:

```bash
# 1) Verify token contains `resource_access.rentella-api`
curl -sS -X POST "http://localhost:8080/realms/rentella/protocol/openid-connect/token" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    --data "grant_type=password&client_id=rentella-web&username=testuser&password=test123" \
    | node -e 'let d="";process.stdin.on("data",c=>d+=c);process.stdin.on("end",()=>{const t=JSON.parse(d).access_token||"";const p=t.split(".")[1]||"";const j=JSON.parse(Buffer.from(p,"base64url").toString("utf8"));console.log(JSON.stringify(j.resource_access||{},null,2));});'

# 2) Ensure user exists in local API DB
docker exec Rentella_app php artisan tinker --execute="dump(App\\Models\\User::where('email','testuser@rentella.local')->exists());"

# 3) If missing, create local user record for strict DB lookup
docker exec Rentella_app php artisan tinker --execute="App\\Models\\User::firstOrCreate(['email'=>'testuser@rentella.local'], ['name'=>'Test User']);"
```

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
