# Rentella API

Laravel 10 backend for the Rentella platform. Handles authentication, listings, bookings, and admin operations.

## Quick start

```bash
cd api
cp .env.example .env

make setup
make data
```

API base URL: `http://localhost:9000/api`

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
