# Development Guide

## Project Structure

```
Rentella/
├── api/                          # Laravel Backend
│   ├── app/
│   │   ├── Models/              # Database models
│   │   ├── Http/
│   │   │   ├── Controllers/     # API endpoints
│   │   │   ├── Requests/        # Form validation
│   │   │   └── Middleware/      # HTTP middleware
│   │   ├── Providers/           # Service providers
│   │   └── Console/             # Artisan commands
│   ├── database/
│   │   ├── migrations/          # Database schema
│   │   ├── seeders/             # Test data
│   │   └── factories/           # Model factories
│   ├── routes/
│   │   ├── api.php              # API routes
│   │   └── web.php              # Web routes
│   ├── config/                  # Configuration files
│   ├── tests/                   # Unit & feature tests
│   ├── storage/                 # Logs, uploads
│   ├── bootstrap/               # Framework bootstrap
│   ├── public/                  # Web-accessible files
│   └── artisan                  # CLI entry point
│
├── web/                          # Vue3 Frontend
│   ├── src/
│   │   ├── pages/               # Page components
│   │   ├── components/          # Reusable components
│   │   │   ├── common/          # Layout, navigation
│   │   │   ├── beach/           # Beach-related
│   │   │   ├── order/           # Order-related
│   │   │   └── ui/              # Generic UI
│   │   ├── stores/              # Pinia state management
│   │   ├── services/            # API, auth services
│   │   ├── types/               # TypeScript interfaces
│   │   ├── utils/               # Helper functions
│   │   ├── router/              # Vue Router config
│   │   ├── App.vue              # Root component
│   │   └── main.ts              # Entry point
│   ├── public/                  # Static assets
│   ├── tests/                   # Component tests
│   ├── tailwind.config.js       # Tailwind config
│   ├── tsconfig.json            # TypeScript config
│   └── vite.config.ts           # Vite config
│
├── deployment/                   # Docker & deployment
│   ├── docker-compose.yml       # Services definition
│   ├── Dockerfile               # Backend image
│   ├── cors.Dockerfile          # CORS proxy
│   └── nginx.conf               # Nginx config
│
├── docker-compose.yml           # Docker Compose (root)
├── Makefile                     # Make commands
├── .gitignore                   # Git ignore patterns
├── README.md                    # Project overview
├── CONTRIBUTING.md              # Contribution guide
├── DEVELOPMENT.md               # This file
└── AUDIT.md                     # Code audit report
```

## Architecture

### Backend (Laravel 10)

**Pattern:** MVC + REST API

```
Request → Route → Controller → Model → Service → Repository → Database
                       ↓
              Middleware (auth, CORS)
                       ↓
                   Response
```

**Key Files:**
- `routes/api.php` - API endpoint definitions
- `app/Http/Controllers/` - Request handlers
- `app/Models/` - Database models
- `app/Http/Requests/` - Input validation
- `config/` - Environment-based config

**Authentication:** Keycloak (OAuth2)

### Frontend (Vue 3)

**Pattern:** Component-based + Composition API

```
main.ts → App.vue → Router → Pages/Components
                         ↓
                    Stores (Pinia)
                         ↓
                    API Services
                         ↓
                    Axios Interceptors
```

**Key Files:**
- `main.ts` - App initialization
- `router/index.ts` - Route definitions
- `pages/` - Route components
- `components/` - Reusable UI components
- `stores/` - Pinia stores (state)
- `services/` - API & Auth services

## Environment Variables

### Backend (.env)
```
APP_ENV=local               # local, staging, production
APP_DEBUG=true              # false in production
DB_HOST=db                  # MySQL host
DB_USERNAME=rentella_user   # MySQL user
DB_PASSWORD=*****           # MySQL password
KEYCLOAK_CLIENT_ID=*        # Keycloak credentials
KEYCLOAK_CLIENT_SECRET=*    # Keycloak credentials
```

### Frontend (.env)
```
VITE_API_URL=http://localhost:9000   # Backend API URL
VITE_KEYCLOAK_URL=http://localhost:8080
VITE_KEYCLOAK_REALM=rentella
VITE_KEYCLOAK_CLIENT_ID=rentella-web
```

## Common Tasks

### Add New API Endpoint

1. **Create Migration** (if needed)
   ```bash
   cd api
   php artisan make:migration create_new_table
   # Edit migration file
   php artisan migrate
   ```

2. **Create Model**
   ```bash
   php artisan make:model NewModel -mf
   # -m: migration, -f: factory
   ```

3. **Create Controller**
   ```bash
   php artisan make:controller NewModelController
   ```

4. **Add Routes** in `routes/api.php`
   ```php
   Route::group(['middleware' => 'auth:api', 'prefix' => 'new-models'], function () {
       Route::get('/', [NewModelController::class, 'index']);
       Route::get('/{id}', [NewModelController::class, 'show']);
       Route::post('/', [NewModelController::class, 'store']);
       // ...
   });
   ```

5. **Implement Controller**
   ```php
   public function index() {
       return response()->json(NewModel::paginate(15));
   }
   ```

### Add New Vue Component

1. **Create Component** in `web/src/components/`
   ```vue
   <script setup lang="ts">
   interface Props { /* ... */ }
   defineProps<Props>()
   </script>
   
   <template>
     <!-- JSX/template -->
   </template>
   ```

2. **Use in Page or Parent**
   ```vue
   <script setup lang="ts">
   import NewComponent from '@/components/NewComponent.vue'
   </script>
   
   <template>
     <NewComponent :prop="value" @event="handler" />
   </template>
   ```

### Add TypeScript Type

Create in `web/src/types/`:

```typescript
// models.ts
export interface Beach {
  id: number
  name: string
  location_id: number
  description: string
  latitude: number
  longitude: number
}

export interface BeachListResponse {
  data: Beach[]
  total: number
  per_page: number
}
```

## Debugging

### Backend
```bash
# View logs
cd api && tail -f storage/logs/laravel.log

# DB queries
php artisan tinker
>>> Beach::first()

# Debug mode
APP_DEBUG=true

# Xdebug with IDE
# Configure IDE to listen on localhost:9003
```

### Frontend
```bash
# Vue DevTools
# Install browser extension

# Console
console.log(), console.error()

# Network
Browser DevTools → Network tab

# Inspect store
Store DevTools (Pinia)

# Component inspection
Vue DevTools extension
```

## Performance Tips

### Backend
- Use eager loading: `with(['relationships'])`
- Add pagination: `paginate(15)`
- Cache frequently accessed data
- Use database indexes
- Monitor N+1 queries

### Frontend
- Lazy load routes: `() => import(...)`
- Use v-if for conditional rendering
- Memoize computed properties
- Debounce API calls
- Compress images

## Testing

### Write Backend Tests
```php
// tests/Feature/BeachControllerTest.php
public function test_beaches_can_be_listed() {
    $response = $this->getJson('/api/beaches');
    $response->assertStatus(200);
}
```

### Write Frontend Tests
```typescript
// web/tests/components/BeachCard.spec.ts
import { mount } from '@vue/test-utils'
import BeachCard from '@/components/beach/BeachCard.vue'

describe('BeachCard', () => {
  it('renders beach name', () => {
    const wrapper = mount(BeachCard, {
      props: { beach: { name: 'Rimini' } }
    })
    expect(wrapper.text()).toContain('Rimini')
  })
})
```

## Deployment Checklist

- [ ] All tests passing
- [ ] No console errors/warnings
- [ ] Environment variables configured
- [ ] Database migrations run
- [ ] Secrets not committed
- [ ] Performance reviewed
- [ ] Security audit passed
- [ ] Documentation updated
- [ ] Changelog updated
- [ ] Version bumped

## Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue 3 Guide](https://vuejs.org)
- [TypeScript Handbook](https://www.typescriptlang.org/docs)
- [Tailwind CSS](https://tailwindcss.com)
- [Keycloak Documentation](https://www.keycloak.org/documentation)

---

**Questions?** Check CONTRIBUTING.md or open an issue.
