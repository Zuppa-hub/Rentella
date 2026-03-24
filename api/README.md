# Rentella API

Laravel 10 backend for the Rentella platform. Handles authentication, listings, bookings, and admin operations.

## Quick start

```bash
cd api
cp .env.example .env

make setup
make data
```

If you already had older local containers/volumes, reset before `make setup`:

```bash
docker-compose -f ./deployment/docker-compose.yml down -v --remove-orphans
```

API base URL: `http://localhost:9000/api`

If Keycloak shows `HTTPS required` during login, run the reset command above and start again.

If protected endpoints return `401` or `403` with strict Keycloak settings, verify both conditions:
- token has `resource_access` for `rentella-api`
- authenticated Keycloak user exists in local `users` table (when DB user loading is enabled)

Quick checks:

```bash
# Check token resource_access
curl -sS -X POST "http://localhost:8080/realms/rentella/protocol/openid-connect/token" \
	-H "Content-Type: application/x-www-form-urlencoded" \
	--data "grant_type=password&client_id=rentella-web&username=testuser&password=test123" \
	| node -e 'let d="";process.stdin.on("data",c=>d+=c);process.stdin.on("end",()=>{const t=JSON.parse(d).access_token||"";const p=t.split(".")[1]||"";const j=JSON.parse(Buffer.from(p,"base64url").toString("utf8"));console.log(JSON.stringify(j.resource_access||{},null,2));});'

# Check / create local user for strict DB lookup
docker exec Rentella_app php artisan tinker --execute="dump(App\\Models\\User::where('email','testuser@rentella.local')->exists());"
docker exec Rentella_app php artisan tinker --execute="App\\Models\\User::firstOrCreate(['email'=>'testuser@rentella.local'], ['name'=>'Test User']);"
```

## Authentication

All protected endpoints require a Bearer token from Keycloak:

```
Authorization: Bearer {token}
```

## Core resources

- Users (profile and bookings)
- Owners (admin only)
- Beaches, zones, umbrellas, prices
- Opening dates, pictures, locations, beach types

## Endpoint groups

Authenticated:
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

## Tests

```bash
docker exec Rentella_app php artisan test tests/Feature/
```

## Configuration

Admin emails:

```
ADMIN_EMAILS=you@example.com,admin@example.com
```

## Technical Debt

- Password reset emails are not delivered in local/dev because Keycloak SMTP is not configured (`docker-compose` and realm import do not define an SMTP server).
- Impact: forgot-password flow completes in UI, but no email is sent.
- Suggested fix: add a local SMTP service (for example MailHog) and configure Keycloak realm SMTP settings.
