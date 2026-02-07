# Rentella

A beach umbrella rental platform. Like Airbnb, but for umbrellas.

Built with Laravel, Vue 3, and Docker.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![Status](https://img.shields.io/badge/status-Production-green)
![License](https://img.shields.io/badge/license-MIT-blue)

## What it does

Beach owners manage umbrella rentals. Users find and book services by location. Everyone stays secure.

## Quick start

### Prerequisites
- Docker & Docker Compose
- Git

### Setup (5 minutes)

```bash
# Clone and navigate
git clone https://github.com/Zuppa-hub/Rentella.git
cd Rentella

# Set up env files
cp api/.env.example api/.env
cp web/.env.example web/.env

# Start everything
make setup

# Load test data
make data
```

Then open:
- Frontend: http://localhost:5173
- API: http://localhost:9000/api
- Keycloak: http://localhost:8080

## How to use

### Make commands

```bash
make setup              # Initial setup
make up                 # Start containers
make down               # Stop containers
make logs               # View logs
make data               # Run migrations & seed database
make data-refresh       # Reset database
```

### Run tests

```bash
docker exec Rentella_app php artisan test tests/Feature/
```

## Stack

### Backend
- Laravel 10 + PHP 8.1
- MySQL 8.0
- Keycloak (OAuth2)
- PHPUnit (40+ tests, all passing)

### Frontend
- Vue 3 + TypeScript
- Vite bundler
- Tailwind CSS
- Pinia state management

### Infrastructure
- Docker + Docker Compose
- Nginx
- PHP-FPM

## API Endpoints

### Authentication required
```
GET/POST   /api/users           - Your profile
GET/POST   /api/beaches         - Beach operations (owner only)
GET/POST   /api/beach-pictures  - Photos (owner only)
GET/POST   /api/beach-zones     - Zones (owner only)
GET/POST   /api/orders          - Your orders only
```

### Admin only
```
GET/POST   /api/owners          - Manage owners
GET/POST   /api/locations       - Manage cities
GET/POST   /api/beach-types     - Manage beach types
```

### Public
```
POST       /api/users           - Register
GET        /api/health          - Health check
```

## Security

- **Phase 1 complete**: Ownership & admin controls implemented
- **40+ tests passing**: All feature tests green
- **User isolation**: Everyone sees only their own data
- More details in [SECURITY.md](./SECURITY.md)

## File structure

```
Rentella/
├── api/                 # Laravel backend
├── web/                 # Vue 3 frontend
├── deployment/          # Docker configs
├── README.md            # This file
├── SECURITY.md          # Security details
└── DEVELOPMENT.md       # For developers
```

## Development

### Backend

```bash
cd api
php artisan tinker              # Interactive shell
php artisan migrate             # Run migrations
php artisan db:seed             # Seed data
php artisan route:list          # List all routes
```

### Frontend

```bash
cd web
npm run dev                      # Dev server with hot reload
npm run build                    # Production build
npm run lint                     # Check code style
```

### Code style
- Backend: PSR-12
- Frontend: ESLint + Prettier
- Commits: Conventional Commits (feat:, fix:, etc.)

## Contributing

Want to help? Read [CONTRIBUTING.md](./CONTRIBUTING.md)

Quick version:
1. Fork
2. Create feature branch: `git checkout -b feature/your-feature`
3. Commit: `git commit -m "feat: describe what you did"`
4. Push and open a PR

## License

MIT

## Contact

Andrea Cazzato - cazzatoandrea@protonmail.com

---

For more details, check [DEVELOPMENT.md](./DEVELOPMENT.md), [SECURITY.md](./SECURITY.md), or existing issues.
