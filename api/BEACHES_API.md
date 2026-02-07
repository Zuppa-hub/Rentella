# API Beaches CRUD Endpoints

## Overview
Fully functional CRUD endpoints for managing beaches with Keycloak authentication and comprehensive request validation.

## Endpoints

### GET /api/beaches
Retrieve all beaches with filtering support
- **Authentication**: Required (Keycloak Bearer token)
- **Status Code**: 200
- **Response**: Array of beaches with pricing and availability info
- **Query Parameters**:
  - `cityId`: Filter by city location
  - `allowed_animals`: Filter by allowed_animals (yes/no)

**Example**:
```bash
curl -X GET "http://localhost:9000/api/beaches?cityId=1&allowed_animals=yes" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### GET /api/beaches/{id}
Retrieve a specific beach with all relationships
- **Authentication**: Required
- **Status Code**: 200
- **Response**: Beach object with owner, location, opening dates, beach type, zones, and prices

**Example**:
```bash
curl -X GET "http://localhost:9000/api/beaches/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### POST /api/beaches
Create a new beach
- **Authentication**: Required
- **Status Code**: 201
- **Request Body**: JSON with beach data

**Required Fields**:
- `owner_id` (integer, must exist in owners table)
- `name` (string, 3-255 characters)
- `location_id` (integer, must exist in city_locations table)
- `opening_date_id` (integer, must exist in opening_dates table)
- `latitude` (numeric, -90 to 90)
- `longitude` (numeric, -180 to 180)
- `allowed_animals` (boolean)
- `type_id` (integer, must exist in beach_types table)

**Optional Fields**:
- `description` (string, max 1000)
- `special_note` (string, max 500)

**Example**:
```bash
curl -X POST "http://localhost:9000/api/beaches" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "owner_id": 1,
    "name": "Beautiful Sunny Beach",
    "location_id": 1,
    "description": "A beautiful beach with white sand",
    "opening_date_id": 1,
    "special_note": "Best time to visit: summer",
    "latitude": 45.5231,
    "longitude": 12.5731,
    "allowed_animals": true,
    "type_id": 1
  }'
```

**Success Response** (201):
```json
{
  "id": 2,
  "owner_id": 1,
  "name": "Beautiful Sunny Beach",
  "location_id": 1,
  "description": "A beautiful beach with white sand",
  "opening_date_id": 1,
  "special_note": "Best time to visit: summer",
  "latitude": 45.5231,
  "longitude": 12.5731,
  "allowed_animals": 1,
  "type_id": 1,
  "created_at": "2024-01-23T10:30:00.000000Z",
  "updated_at": "2024-01-23T10:30:00.000000Z"
}
```

**Validation Error Response** (422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "owner_id": ["Owner not found"],
    "latitude": ["Latitude must be between -90 and 90"]
  }
}
```

### PUT /api/beaches/{id}
Update an existing beach
- **Authentication**: Required
- **Status Code**: 200
- **Request Body**: JSON with fields to update (all fields are optional)

**Example**:
```bash
curl -X PUT "http://localhost:9000/api/beaches/1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "Updated Beach Name",
    "description": "Updated description",
    "allowed_animals": false
  }'
```

**Success Response** (200):
```json
{
  "id": 1,
  "owner_id": 1,
  "name": "Updated Beach Name",
  "location_id": 1,
  "description": "Updated description",
  "opening_date_id": 1,
  "special_note": "Best time to visit: summer",
  "latitude": 45.5231,
  "longitude": 12.5731,
  "allowed_animals": 0,
  "type_id": 1,
  "created_at": "2024-01-23T10:20:00.000000Z",
  "updated_at": "2024-01-23T10:35:00.000000Z"
}
```

### DELETE /api/beaches/{id}
Delete a beach
- **Authentication**: Required
- **Status Code**: 204 No Content
- **Response**: Empty (204 status code indicates success)

**Example**:
```bash
curl -X DELETE "http://localhost:9000/api/beaches/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Request Validation

### StoreBeachRequest (POST)
Validates all required fields for creating a beach. All foreign key fields must reference existing records.

**Validation Rules**:
- `owner_id`: required, integer, must exist in owners table
- `name`: required, string, 3-255 characters
- `location_id`: required, integer, must exist in city_locations table
- `description`: optional, string, max 1000 characters
- `opening_date_id`: required, integer, must exist in opening_dates table
- `special_note`: optional, string, max 500 characters
- `latitude`: required, numeric, between -90 and 90
- `longitude`: required, numeric, between -180 and 180
- `allowed_animals`: required, boolean
- `type_id`: required, integer, must exist in beach_types table

### UpdateBeachRequest (PUT)
Validates fields during updates. All fields are optional (uses `sometimes|required` pattern).

**Validation Rules**: Same as StoreBeachRequest, but all fields are optional

## Error Responses

### 401 Unauthorized
Missing or invalid authentication token
```json
{
  "message": "Unauthenticated."
}
```

### 404 Not Found
Beach not found
```json
{
  "message": "Not found."
}
```

### 422 Unprocessable Entity
Validation errors
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

## Getting a Bearer Token

### Using Keycloak (Production)
```bash
TOKEN=$(curl -X POST \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=password&client_id=rentella-api&username=testuser&password=test123" \
  http://localhost:8080/realms/rentella/protocol/openid-connect/token | jq -r '.access_token')

echo $TOKEN
```

## Testing Endpoints

### Install HTTPie (optional, makes testing easier)
```bash
brew install httpie  # macOS
apt-get install httpie  # Linux
```

### Test GET /api/beaches
```bash
http GET http://localhost:9000/api/beaches "Authorization: Bearer YOUR_TOKEN"
```

### Test POST /api/beaches
```bash
http POST http://localhost:9000/api/beaches \
  owner_id:=1 \
  name="Test Beach" \
  location_id:=1 \
  opening_date_id:=1 \
  latitude:=45.5 \
  longitude:=12.5 \
  allowed_animals:=true \
  type_id:=1 \
  "Authorization: Bearer YOUR_TOKEN"
```

### Test PUT /api/beaches/{id}
```bash
http PUT http://localhost:9000/api/beaches/1 \
  name="Updated Name" \
  "Authorization: Bearer YOUR_TOKEN"
```

### Test DELETE /api/beaches/{id}
```bash
http DELETE http://localhost:9000/api/beaches/1 \
  "Authorization: Bearer YOUR_TOKEN"
```

## Implementation Details

### Files Modified
- `app/Http/Controllers/BeachController.php`: Updated CRUD methods with proper response codes and relationship loading
- `tests/Feature/BeachesApiTest.php`: Comprehensive test suite for all CRUD operations

### Files Created
- `app/Http/Requests/StoreBeachRequest.php`: Validation for POST requests
- `app/Http/Requests/UpdateBeachRequest.php`: Validation for PUT requests

### Key Features
- ✅ Full CRUD operations (Create, Read, Update, Delete)
- ✅ Request validation with custom error messages
- ✅ Foreign key validation
- ✅ Proper HTTP status codes (201 for create, 204 for delete)
- ✅ Relationship eager loading (owner, location, opening date, beach type, zones)
- ✅ Comprehensive test coverage
- ✅ Keycloak authentication on all protected endpoints
