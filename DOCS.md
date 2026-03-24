# Documentation

## Useful commands

### Container management

```bash
# Start services
make -C api up

# Stop services
make -C api stop

# Run migrations and seeds
make -C api data

# Rollback last migration
make -C api rollback

# Create a new migration
make -C api new_migration create_beaches_table
```

## API overview

All endpoints require a Bearer token from Keycloak (except public endpoints).

### Authentication required

- `/api/users` — User profiles and account management
- `/api/beaches` — Beach listings and details
- `/api/beach-pictures` — Beach photos
- `/api/beach-zones` — Beach zones (e.g., areas with umbrellas)
- `/api/umbrellas` — Umbrella inventory
- `/api/prices` — Pricing rules
- `/api/opening-dates` — Operating dates
- `/api/orders` — User bookings/orders

### Admin only

- `/api/owners` — Beach owner management
- `/api/locations` — Geographic locations
- `/api/beach-types` — Beach type categories

### Public (no auth required)

- `POST /api/users` — User registration
- `GET /api/health` — API health check

## Making API requests

All authenticated endpoints require the `Authorization` header:

```bash
Authorization: Bearer {jwt_token}
```

Example:

```bash
curl -H "Authorization: Bearer $TOKEN" http://localhost:9000/api/beaches
```

## Environment variables

### Backend (api/.env)

```
APP_ENV=production              # Set to 'local' for development
APP_DEBUG=false                 # Never true in production
ADMIN_EMAILS=admin@example.com  # Comma-separated admin email list

DB_HOST=mysql_db                # Database host
KEYCLOAK_BASE_URL=http://keycloak:8080  # Keycloak URL
```

### Frontend (web/.env)

```
VITE_KEYCLOAK_URL=http://localhost:8080
VITE_KEYCLOAK_REALM=rentella
VITE_KEYCLOAK_CLIENT_ID=rentella-web
VITE_KEYCLOAK_REDIRECT_URI=http://localhost:5173/
```

## Testing

Run the full test suite:

```bash
docker exec Rentella_app php artisan test tests/Feature/
```

Run a specific test:

```bash
docker exec Rentella_app php artisan test tests/Feature/SecurityTest.php
```

## Debugging

### View API logs

```bash
docker logs -f Rentella_app | grep -E "(ERROR|WARNING|token)"
```

### View Keycloak logs

```bash
docker logs -f deployment-keycloak-1
```

### Database access

MySQL is available at `localhost:3306`:
- User: `root`
- Password: (from `.env` `MYSQL_ROOT_PASSWORD`)
- Database: `Rentella_db`

Or use phpMyAdmin at `http://localhost:9001`

## Known issues

- **Keycloak SMTP not configured**: Password reset emails are not sent in local/dev. Fix: add MailHog or configure SMTP in Keycloak realm settings.
- **`sslRequired: none`**: Local development only. Production must use HTTPS with `sslRequired: external` or `all`.
