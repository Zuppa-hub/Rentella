# Contributing to Rentella

## Code of Conduct
Please be respectful and constructive in all interactions.

## Development Setup

### Prerequisites
- Docker & Docker Compose
- Git
- Node.js 18+ (for local development)
- PHP 8.1+ (for local development)

### Quick Start

1. **Clone and setup**
   ```bash
   git clone <repo>
   cd Rentella
   cp api/.env.example api/.env
   cp web/.env.example web/.env
   ```

2. **Start Docker containers**
   ```bash
   make setup
   ```

3. **Install dependencies**
   ```bash
   cd api && composer install
   cd ../web && npm install
   ```

4. **Generate app key**
   ```bash
   cd api && php artisan key:generate
   ```

5. **Run migrations**
   ```bash
   cd api && php artisan migrate --seed
   ```

6. **Start development servers**
   ```bash
   # Terminal 1 - Backend
   cd api && php artisan serve --port=9000
   
   # Terminal 2 - Frontend
   cd web && npm run dev
   ```

### Docker Commands
```bash
make setup          # Build and start all containers
make build          # Build containers
make up             # Start containers
make down           # Stop containers
make logs           # View container logs
make composer-update # Update PHP dependencies
```

## Git Workflow

1. **Create a feature branch**
   ```bash
   git checkout -b feature/description
   ```

2. **Commit with conventional commits**
   ```bash
   git commit -m "feat: add new feature"
   git commit -m "fix: resolve issue"
   git commit -m "refactor: improve code"
   ```

3. **Push and create PR**
   ```bash
   git push origin feature/description
   ```

## Commit Message Format

Use Conventional Commits:
- `feat: ` - New feature
- `fix: ` - Bug fix
- `docs: ` - Documentation
- `style: ` - Code style (no logic changes)
- `refactor: ` - Code refactoring
- `perf: ` - Performance improvements
- `test: ` - Tests
- `chore: ` - Build, dependencies
- `ci: ` - CI/CD changes

Example:
```bash
git commit -m "feat: add beach filter by location

- Implements location filtering on beaches list
- Adds geolocation support
- Updates API endpoint with new parameters

Closes #123"
```

## Code Standards

### PHP (Backend)
- Follow PSR-12 standards
- Use meaningful variable names
- Add PHPDoc comments
- Max line length: 120 characters

### TypeScript/Vue (Frontend)
- Use TypeScript strict mode
- Follow Vue 3 Composition API patterns
- Add proper type definitions
- Use PascalCase for component names

### General
- Write self-documenting code
- Add comments for complex logic
- Keep functions small and focused
- No console.log in production

## Testing

### Backend
```bash
cd api
php artisan test                    # Run all tests
php artisan test --filter=Beach    # Run specific test
php artisan test --coverage        # Generate coverage report
```

### Frontend
```bash
cd web
npm run test                        # Run all tests
npm run test:coverage              # Coverage report
npm run test:watch                 # Watch mode
```

## Before Submitting PR

- [ ] Code follows project standards
- [ ] All tests pass (`npm run test`)
- [ ] No console errors/warnings
- [ ] Linting passes (`npm run lint`)
- [ ] Documentation updated if needed
- [ ] Commit messages are descriptive
- [ ] No secrets committed
- [ ] Branch is up-to-date with main

## Need Help?

- Check existing issues/discussions
- Review ARCHITECTURE.md for design patterns
- Ask in PR comments or issues
- Contact maintainers

---

**Happy coding! 🚀**
