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

**Rentella** is a comprehensive beach service rental platform that enables beach owners to list and manage umbrella rentals, zones, and pricing. Users can browse available beaches by location, view umbrella availability, check prices, and place orders.

### Key Capabilities

- 🏖️ Browse beaches by location with geolocation support
- ☂️ View umbrella availability and pricing
- 🗺️ Interactive map-based location selection
- 💳 Order management and history
- 🔐 Secure authentication via Keycloak
- 📱 Responsive mobile-first design
- 🌍 Multi-location support

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

- **[DEVELOPMENT.md](./DEVELOPMENT.md)** - Architecture, setup guide, and common tasks
- **[CONTRIBUTING.md](./CONTRIBUTING.md)** - Code standards, workflow, and contribution process
- **[AUDIT.md](./AUDIT.md)** - Comprehensive code quality audit and improvement roadmap

### API Documentation

The API follows RESTful conventions with the following endpoints:

```
/api/users              - User management
/api/locations          - City locations with geolocation
/api/beaches            - Beach listings and details
/api/beach-pictures     - Beach photos
/api/beach-types        - Beach type classification
/api/beach-zones        - Beach zones/areas
/api/opening-dates      - Operating hours
/api/orders             - Order management
/api/owners             - Beach owner management
/api/prices             - Pricing configuration
/api/umbrellas          - Umbrella inventory
```

All endpoints require authentication via Keycloak tokens (Bearer JWT).

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

### Local Development Setup

```bash
# Backend development
cd api
php artisan serve --port=9000

# Frontend development (separate terminal)
cd web
npm run dev
```

### Development Commands

```bash
# Backend
cd api
php artisan tinker              # Interactive shell
php artisan migrate             # Run migrations
php artisan db:seed             # Seed database
php artisan test                # Run tests
php artisan route:list          # View all routes

# Frontend
cd web
npm run dev                      # Start dev server
npm run build                    # Production build
npm run preview                  # Preview build locally
npm run lint                     # ESLint check
npm run format                   # Format with Prettier
npm run test                     # Run tests
```

### Code Style

- **Backend:** PSR-12
- **Frontend:** ESLint + Prettier
- **Git commits:** Conventional Commits

For detailed development guide, see [DEVELOPMENT.md](./DEVELOPMENT.md)

---

## Deployment

### Docker Deployment

The project is fully containerized and ready for production deployment:

```bash
# Production build
docker-compose -f deployment/docker-compose.yml build

# Start services
docker-compose -f deployment/docker-compose.yml up -d

# Run migrations
docker exec rentella_app php artisan migrate --force

# Seed data (if needed)
docker exec rentella_app php artisan db:seed
```

### Environment Variables

Create `.env` files from `.env.example`:
- `api/.env` - Backend configuration
- `web/.env` - Frontend configuration

**Critical variables to configure:**
- Database credentials
- Keycloak credentials
- API base URLs

### Monitoring

Monitor logs in real-time:
```bash
docker-compose logs -f app
docker-compose logs -f web
docker-compose logs -f db
```

---

## Current Status

### Code Quality
- **Overall Score:** 3/10 (needs improvements)
- **Backend:** 6/10 (functional but needs testing)
- **Frontend:** 3/10 (significant refactoring needed)

### Known Issues
See [AUDIT.md](./AUDIT.md) for comprehensive audit with:
- 5 critical issues (requires immediate fixes)
- 40+ issues across backend and frontend
- Detailed remediation roadmap

### Improvement Roadmap

**Phase 1 - Critical Fixes** (1-2 weeks)
- Input validation & security hardening
- CORS restriction
- Environment configuration
- API URLs externalization

**Phase 2 - Architecture** (2-3 weeks)
- State management implementation
- API service refactoring
- Component TypeScript migration
- Route lazy loading

**Phase 3 - Quality** (2-3 weeks)
- Test suite implementation
- ESLint + Prettier setup
- Security hardening
- Performance optimization

**Phase 4 - Production** (1-2 weeks)
- CI/CD pipeline setup
- Monitoring & logging
- Database backups
- Documentation completion

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

### v0.0.0 (Current)
- Initial project setup
- Core API endpoints implemented
- Vue 3 frontend with basic views
- Docker containerization
- Keycloak integration (WIP)

For detailed changes, see git commit history.

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
