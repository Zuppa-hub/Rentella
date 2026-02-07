# Development

## Getting started

### How to set up locally

```bash
# Clone the repo
git clone https://github.com/Zuppa-hub/Rentella.git
cd Rentella

# Copy env files
cp api/.env.example api/.env
cp web/.env.example web/.env

# Start Docker
make setup

# Run migrations and seed data
make data
```

That's it. Everything runs on Docker.

## File structure

```
api/
├── app/
│   ├── Models/                 # Database models
│   ├── Http/
│   │   ├── Controllers/        # API endpoints (12 total)
│   │   ├── Requests/           # Form validation
│   │   └── Middleware/         # Auth & CORS
│   ├── Exceptions/             # Error handling
│   └── Providers/              # Service setup
├── database/
│   ├── migrations/             # Schema changes
│   ├── seeders/                # Test data
│   └── factories/              # Model factories for tests
├── routes/api.php              # All API routes here
├── config/app.php              # App config (includes admin_emails)
└── tests/                      # All tests

web/
├── src/
│   ├── pages/                  # Route-level components
│   ├── components/             # Reusable components
│   ├── stores/                 # Pinia state
│   ├── services/               # API & Auth services
│   ├── router/                 # Vue Router setup
│   └── types/                  # TypeScript types
├── public/                     # Static assets
└── tests/                      # Component tests
```

## Models & relationships

### Core models
- **User**: Auth user (from Keycloak)
- **Owner**: Beach owner (separate from User)
- **Beach**: A beach with location & owner
- **BeachZone**: Areas within a beach
- **Umbrella**: Individual umbrellas
- **Price**: Pricing for zones
- **Order**: User bookings
- **OpeningDate**: Operating schedule
- **BeachPicture**: Photos
- **BeachType**: Classification
- **CityLocation**: City/region

All relationships are documented in models.

## Running tests

### All tests
```bash
docker exec Rentella_app php artisan test tests/Feature/
```

### Specific test
```bash
docker exec Rentella_app php artisan test tests/Feature/DeepSecurityAuditTest.php
```

### With coverage
```bash
docker exec Rentella_app php artisan test --coverage
```

Current: 40+ tests, all passing.

## Useful commands

### Database
```bash
make data              # Migrate & seed
make data-refresh      # Fresh database
make rollback          # Undo last migration
```

### Code
```bash
cd api
php artisan tinker               # Interactive shell
php artisan route:list           # View all routes
php artisan migrate --step       # Run one migration at a time
php artisan make:migration name  # Create new migration
php artisan make:controller name # Create new controller
```

### Frontend
```bash
cd web
npm run dev            # Dev server (port 5173)
npm run build          # Production build
npm run lint           # Code style check
npm run preview        # Preview built app
```

## Code style

### Backend (Laravel)
- PSR-12 standard
- Format: `./vendor/bin/pint` (or manual)
- Naming: camelCase for methods, snake_case for database

### Frontend (Vue 3)
- ESLint + Prettier
- TypeScript strict mode
- Naming: PascalCase for components, camelCase for functions

### Commits
Use conventional commits:
- `feat: add new feature`
- `fix: bug fix`
- `docs: documentation`
- `style: code style (no logic change)`
- `refactor: restructure code`
- `test: test changes`
- `chore: maintenance`

Example: `git commit -m "feat: add ownership validation to pictures controller"`

## Database

### How to run a migration

```bash
docker exec Rentella_app php artisan migrate
```

### How to create a new migration

```bash
docker exec Rentella_app php artisan make:migration create_table_name
# Edit the file
docker exec Rentella_app php artisan migrate
```

### How to seed data

```bash
docker exec Rentella_app php artisan db:seed
# Or specific seeder
docker exec Rentella_app php artisan db:seed --class=BeachSeeder
```

## Contributing

1. Create a feature branch: `git checkout -b feature/description`
2. Make changes
3. Write tests for new features
4. Ensure all tests pass: `make test`
5. Commit with conventional message
6. Push and open a PR

See [CONTRIBUTING.md](./CONTRIBUTING.md) for details.

## Common issues

### Tests failing after env change
- Rebuild containers: `make down && make up`
- Clear cache: `docker exec Rentella_app php artisan cache:clear`

### Port already in use
- Change in `.env` or kill existing process
- Frontend default: 5173
- Backend default: 9000
- Keycloak: 8080

### Database connection error
- Check `.env` DATABASE_* settings
- Ensure MySQL is running: `docker ps | grep mysql`
- Restart: `make down && make up && make data`

## Architecture notes

### Authentication
- Keycloak handles login/token
- Every API request needs Bearer token
- Verified in `Authenticate` middleware
- User info available via `auth()->user()`

### Ownership checks
Pattern used across controllers:
```php
if ($model->owner->email !== auth()->user()->email) {
    return response()->json(['error' => 'Unauthorized'], 403);
}
```

### API responses
Standard format:
```json
{
  "data": {...},
  "status": 200,
  "message": "Success"
}
```

Errors:
```json
{
  "error": "Message",
  "status": 400
}
```

## Deployment

See [Docker setup](./deployment/) for production configuration.

Key files:
- `deployment/docker-compose.yml` - Services definition
- `deployment/Dockerfile` - Backend image
- `deployment/nginx.conf` - Web server config

To deploy:
```bash
docker-compose -f deployment/docker-compose.yml up -d
docker exec Rentella_app php artisan migrate --force
```

## Need help?

- Check existing issues: https://github.com/Zuppa-hub/Rentella/issues
- Read code comments
- Run tests to understand expected behavior
- Ask in PRs or issues
