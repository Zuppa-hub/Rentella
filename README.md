# Rentella - Beach Umbrella Rental Platform

> An Airbnb-like platform for renting beach umbrellas and ancillary services, built with Laravel, Vue 3, and Docker.

![Version](https://img.shields.io/badge/version-0.0.0-blue)
![Status](https://img.shields.io/badge/status-In%20Development-yellow)
![License](https://img.shields.io/badge/license-MIT-green)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
- [Project Structure](#project-structure)
- [Features](#features)
- [Development](#development)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [Status](#status)
- [License](#license)

---

## Overview

**Rentella** is a secure beach service rental API where beach owners manage umbrella rentals and users browse/book services by location.

### What's Working ✅
- Secure authentication (Keycloak)
- Ownership-based access control
- User data isolation (orders, profiles)
- Admin-restricted endpoints
- 40 integration tests (all passing)
- RESTful API for mobile/web clients

### Key Features
- 🏖️ Beach browsing by location
- ☂️ Umbrella inventory management
- 🔐 Secure owner-only beach operations
- 👥 User profiles with isolation
- 💳 Order tracking (user-specific)
- 🗺️ Geolocation support

---

## Tech Stack

### Backend
- **Framework:** Laravel 10.x (PHP 8.1+)
- **Database:** MySQL 8.0
- **Auth:** Keycloak (OAuth2)
- **Cache:** File-based (configurable)
- **API:** RESTful with JSON responses
- **Testing:** PHPUnit (ready for implementation)

### Frontend
- **Framework:** Vue 3.x with TypeScript
- **Bundler:** Vite 4.x
- **Styling:** Tailwind CSS 3.x
- **Maps:** Leaflet + Vue Leaflet
- **Auth:** Keycloak JS
- **HTTP Client:** Axios (ready for implementation)
- **State:** Pinia (ready for implementation)
- **Testing:** Vitest (ready for implementation)

### Infrastructure
- **Containerization:** Docker + Docker Compose
- **Web Server:** Nginx
- **PHP Runtime:** PHP-FPM 8.1
- **Reverse Proxy:** CORS proxy container

---

## Quick Start

### Prerequisites

- Docker & Docker Compose
- Git
- (Optional) Node.js 18+ and PHP 8.1+ for local development

### Setup (5 minutes)

1. **Clone repository**
   ```bash
   git clone https://github.com/yourusername/rentella.git
   cd rentella
   ```

2. **Copy environment files**
   ```bash
   cp api/.env.example api/.env
   cp web/.env.example web/.env
   ```

3. **Start Docker containers**
   ```bash
   make setup
   ```
   This will:
   - Build Docker images
   - Start all services (Laravel, MySQL, Keycloak, Nginx)
   - Install PHP dependencies
   - Create database

4. **Initialize database**
   ```bash
   make data
   ```
   This will run migrations and seed sample data.

5. **Access application**
   - Frontend: http://localhost:5173 (Vite dev server)
   - Backend API: http://localhost:9000/api
   - Keycloak: http://localhost:8080
   - phpMyAdmin: http://localhost:9001

### Make Commands

```bash
make setup              # Setup and start all containers (full init)
make build              # Build Docker images
make up                 # Start containers
make down               # Stop containers
make logs               # View container logs
make logs-app           # View app logs only
make composer-install   # Install PHP dependencies
make composer-update    # Update PHP dependencies
make data               # Run migrations & seed database
make data-refresh       # Refresh database (reset + seed)
make rollback           # Rollback last migration
make migrations          # List all migrations
make new-migration NAME # Create new migration
make tinker             # Enter Laravel Tinker REPL
```

---

## Documentation

- **[SECURITY_AUDIT_DEEP.md](./SECURITY_AUDIT_DEEP.md)** - Security fixes & implementation details (Phase 1)
- **[DEVELOPMENT.md](./DEVELOPMENT.md)** - Architecture and development guide
- **[CONTRIBUTING.md](./CONTRIBUTING.md)** - Contribution workflow

### Quick API Reference

**Authenticated endpoints** (require Keycloak JWT):
```
GET/POST   /api/users              - User profile management
GET/POST   /api/beaches            - Beach CRUD (owner restrictions)
GET/POST   /api/beach-pictures     - Pictures (owner only)
GET/POST   /api/beach-zones        - Zones (owner only)
GET/POST   /api/orders             - Orders (user's own only)
```

**Admin-only endpoints:**
```
GET/POST   /api/owners             - Owner management
GET/POST   /api/locations          - City locations
GET/POST   /api/beach-types        - Beach types
```

**Public endpoints:**
```
POST       /api/users              - User registration
GET        /api/health             - Health check
```

---

## Project Structure

```
rentella/
├── api/                              # Laravel Backend
│   ├── app/
│   │   ├── Models/                  # Database models (Beach, Order, etc)
│   │   ├── Http/
│   │   │   ├── Controllers/         # 12 CRUD controllers
│   │   │   ├── Requests/            # Form request validation
│   │   │   └── Middleware/          # Auth & CORS middleware
│   │   └── Providers/               # Service providers
│   ├── database/
│   │   ├── migrations/              # Database schema
│   │   ├── seeders/                 # Sample data
│   │   └── factories/               # Model factories for testing
│   ├── routes/
│   │   ├── api.php                  # API route definitions
│   │   └── web.php                  # Web routes
│   ├── config/                      # Laravel configuration
│   ├── storage/
│   │   ├── logs/                    # Application logs
│   │   └── app/                     # File uploads
│   └── tests/                       # PHPUnit tests
│
├── web/                              # Vue 3 Frontend
│   ├── src/
│   │   ├── pages/                   # Route-level components
│   │   ├── components/              # Reusable UI components
│   │   ├── stores/                  # Pinia state management
│   │   ├── services/                # API & Auth services
│   │   ├── types/                   # TypeScript interfaces
│   │   ├── router/                  # Vue Router configuration
│   │   ├── assets/                  # Static files
│   │   ├── App.vue                  # Root component
│   │   └── main.ts                  # Application entry
│   ├── public/                      # Static assets
│   ├── tests/                       # Vitest tests
│   ├── vite.config.ts               # Vite configuration
│   ├── tailwind.config.js           # Tailwind CSS config
│   └── tsconfig.json                # TypeScript config
│
├── deployment/                       # Docker & Infrastructure
│   ├── docker-compose.yml           # Services definition
│   ├── Dockerfile                   # Backend image
│   ├── cors.Dockerfile              # CORS proxy
│   └── nginx.conf                   # Nginx configuration
│
├── .gitignore                       # Git ignore patterns
├── Makefile                         # Make commands
├── README.md                        # This file
├── CONTRIBUTING.md                  # Contribution guide
├── DEVELOPMENT.md                   # Development guide
└── AUDIT.md                         # Code audit report
```

---

## Features

### Core Features ✅
- [x] Beach listing and search
- [x] Geolocation-based beach discovery
- [x] Beach detail pages with pictures
- [x] Umbrella availability display
- [x] Dynamic pricing by zone/season
- [x] User authentication (Keycloak)
- [x] Order history
- [x] Mobile-responsive design

### In Progress 🔄
- [ ] Order placement system
- [ ] Payment integration
- [ ] Owner dashboard
- [ ] Advanced filtering
- [ ] Reviews and ratings

### Planned 📋
- [ ] Real-time availability
- [ ] Calendar booking system
- [ ] Seasonal pricing automation
- [ ] Analytics dashboard
- [ ] Mobile app (React Native)
- [ ] API v2 with GraphQL

---

## Development

### Running Tests

```bash
# All feature tests
docker exec Rentella_app php artisan test tests/Feature/

# Specific test suite
docker exec Rentella_app php artisan test tests/Feature/DeepSecurityAuditTest.php
docker exec Rentella_app php artisan test tests/Feature/SecurityAuditTest.php
```

### Backend Commands

```bash
cd api
php artisan tinker              # Interactive shell
php artisan migrate             # Run migrations
php artisan db:seed             # Seed database
php artisan route:list          # View all routes
```

### Frontend Commands

```bash
cd web
npm run dev                      # Dev server
npm run build                    # Production build
npm run lint                     # Check code style
```

### Code Style
- **Backend:** PSR-12 (Laravel standard)
- **Frontend:** ESLint + Prettier
- **Commits:** Conventional Commits (`feat:`, `fix:`, `docs:`, etc.)

---

## Deployment

### Quick Docker Start

```bash
# Build and start all services
docker-compose -f deployment/docker-compose.yml up -d

# Run migrations
docker exec Rentella_app php artisan migrate --force

# View logs
docker-compose logs -f app
```

### Services Running
- **API:** http://localhost:9000/api
- **Frontend:** http://localhost:5173
- **Keycloak:** http://localhost:8080
- **phpMyAdmin:** http://localhost:9001

### Configuration
1. Copy `.env.example` to `.env` in both `api/` and `web/`
2. Update **database**, **Keycloak**, and **API** credentials
3. Set `ADMIN_EMAILS` for admin-only endpoints (comma-separated)

---

## Current Status

### ✅ Phase 1 Complete: Security Hardening
- 40+ tests passing (all feature tests green)
- **Ownership validation** implemented for beach operations (pictures, zones, umbrellas, prices, dates)
- **Admin-only endpoints** restricted (owners, locations, beach types)
- **User isolation** enforced (users see only own profile, orders, data)
- Deep security audit completed with 12 critical vulnerability fixes
- See [SECURITY_AUDIT_DEEP.md](./SECURITY_AUDIT_DEEP.md) for full details

### Code Quality Overview
| Area | Score | Status |
|------|-------|--------|
| **Backend API** | 8/10 | ✅ Secure, tested |
| **Security** | 9/10 | ✅ Auth, ownership checks |
| **Frontend** | 4/10 | 🔄 Needs refactor (Phase 2) |
| **Tests** | 10/10 | ✅ 40 tests passing |

### Improvement Roadmap

**Phase 1 - Security** ✅ DONE
- Input validation & auth
- Ownership & admin checks
- Comprehensive test suite

**Phase 2 - Frontend** (Next)
- Vue 3 component refactor
- TypeScript migration
- State management (Pinia)

**Phase 3 - Quality** 
- Performance optimization
- Error handling & logging
- Documentation

---

## Contributing

We welcome contributions! Please read [CONTRIBUTING.md](./CONTRIBUTING.md) for:
- Development setup instructions
- Code standards and style guide
- Git workflow and commit conventions
- Pull request process
- Testing requirements

### Quick Contribution Steps

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit with conventional messages: `git commit -m "feat: add new feature"`
4. Push and open a Pull Request

---

## Architecture Highlights

### Authentication Flow
```
User → Keycloak → JWT Token → API (Bearer Auth)
						  ↓
					Protected Routes
```

### Database Schema
- 11 core entities with relationships
- Foreign key constraints
- Seeded with sample data for development

### API Response Format
```json
{
  "data": [...],
  "status": 200,
  "message": "Success"
}
```

---

## Support & Issues

- 📖 Check [DEVELOPMENT.md](./DEVELOPMENT.md) for common tasks
- 🐛 Found a bug? Open an issue with reproduction steps
- 💡 Have a feature idea? Start a discussion
- ❓ Need help? Check existing issues or contact maintainers

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## Changelog

### v0.1.0 - Phase 1: Security ✅
- Ownership validation for beach operations
- Admin-only endpoints (owners, locations, beach types)
- User isolation enforced (orders, profiles)
- Deep security audit with 12 fixes
- 40 feature tests (all passing)
- Request validation hardening

### v0.0.0 (Initial)
- Core API endpoints
- Vue 3 frontend
- Docker containerization
- Keycloak integration

---

## Project Timeline

- **Started:** August 28, 2023
- **Last Updated:** January 19, 2026
- **Status:** In active development

---

## Contact & Credits

**Project Lead:** Andrea Cazzato  
**Email:** cazzatoandrea@protonmail.com

---

**Happy coding! 🚀**

For questions or suggestions, open an issue or reach out to the maintainers.
