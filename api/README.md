# API

The backend. Built with Laravel 10.

## Quick start

```bash
cd api
cp .env.example .env
docker-compose up -d
php artisan migrate
php artisan db:seed
```

API runs on `http://localhost:9000/api`

## Architecture

### Controllers (12 total)

**Read-only (public with auth):**
- `BeachController` - Browse beaches
- `UserController` - Your profile

**Ownership required:**
- `BeachPictureController` - Photos
- `BeachZonesController` - Zones
- `UmbrellasController` - Umbrellas
- `OpeningDatesController` - Hours
- `PricesController` - Pricing
- `OrdersController` - Bookings

**Admin only:**
- `OwnersController` - Manage owners
- `LocationController` - Manage cities
- `BeachTypeController` - Manage types

### Models

```
Owner
├── Beach
│   ├── BeachPicture
│   ├── BeachZone
│   │   ├── Umbrella
│   │   └── Price
│   ├── OpeningDate
│   └── BeachType
└── BeachType

User
└── Order
    └── Beach
```

Relationships all in `app/Models/`

### Routes

All API routes defined in `routes/api.php`

Middleware stack:
1. Auth - Keycloak JWT verification
2. CORS - Allow frontend requests
3. Rate limit - Prevent abuse

### Authentication

Every request needs:
```
Authorization: Bearer {token}
```

Token from Keycloak. Verified in `app/Http/Middleware/Authenticate.php`

Get user info:
```php
$user = auth()->user();  // Returns User model
$email = auth()->user()->email;
```

## Database

11 main tables:
- users
- owners
- beaches
- beach_pictures
- beach_zones
- umbrellas
- prices
- orders
- opening_dates
- beach_types
- city_locations

All have timestamps. Foreign keys enforced.

Run migrations:
```bash
docker exec Rentella_app php artisan migrate
```

Seed test data:
```bash
docker exec Rentella_app php artisan db:seed
```

## Testing

40+ tests covering all endpoints.

Run them:
```bash
docker exec Rentella_app php artisan test tests/Feature/
```

Test structure:
- `Feature/ApiHealthCheckTest.php` - Endpoint smoke tests
- `Feature/DeepSecurityAuditTest.php` - Security validation
- More in `tests/`

## Configuration

Key settings in `config/app.php`:

```php
'admin_emails' => explode(',', env('ADMIN_EMAILS', 'admin@rentella.com'))
```

Set in `.env`:
```
ADMIN_EMAILS=you@example.com,admin@example.com
```

## Useful commands

```bash
# Interactive shell
php artisan tinker

# View all routes
php artisan route:list

# Check migrations status
php artisan migrate:status

# Create new migration
php artisan make:migration create_table_name

# Create new controller
php artisan make:controller NameController --model=Model

# Run specific test
php artisan test tests/Feature/SomeTest.php
```

## Error handling

All errors standardized:

```json
{
  "error": "Unauthorized",
  "status": 401
}
```

Status codes:
- `200` - OK
- `201` - Created
- `400` - Bad request
- `401` - Unauthorized (bad token)
- `403` - Forbidden (not owner/admin)
- `404` - Not found
- `422` - Validation error
- `500` - Server error

## Performance

Database queries optimized:
- Relationship eager loading where needed
- Indexed foreign keys
- Query builder for efficiency

No N+1 queries in main endpoints.

## Security

- No raw SQL queries
- Input validation via Form Requests
- Ownership checks on all write operations
- Admin checks on system endpoints
- CORS properly configured

Details in [SECURITY.md](../SECURITY.md)

## Development tips

### Working with relationships

Get related data efficiently:
```php
// Don't do this
$beaches = Beach::all();
foreach ($beaches as $beach) {
    echo $beach->owner->name;  // N+1 query!
}

// Do this
$beaches = Beach::with('owner')->get();
foreach ($beaches as $beach) {
    echo $beach->owner->name;  // Already loaded
}
```

### Adding a new endpoint

1. Create migration if needed
2. Create controller method
3. Add route
4. Write tests
5. Document in README

Example:
```php
// routes/api.php
Route::get('/beaches/{beach}', [BeachController::class, 'show']);

// app/Http/Controllers/BeachController.php
public function show(Beach $beach): JsonResponse
{
    return response()->json($beach);
}

// tests/Feature/BeachTest.php
public function test_can_view_beach()
{
    $beach = Beach::factory()->create();
    $response = $this->getJson("/api/beaches/{$beach->id}");
    $response->assertStatus(200);
}
```

## Deployment

See `deployment/` folder for Docker setup.

Quick start:
```bash
docker-compose -f deployment/docker-compose.yml up -d
docker exec Rentella_app php artisan migrate --force
```

## That's it

Read the code. It's well-documented. Questions? Check tests - they show usage patterns.
