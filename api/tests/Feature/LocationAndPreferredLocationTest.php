<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\CityLocation;
use Illuminate\Support\Str;

class LocationAndPreferredLocationTest extends TestCase
{
    private ?User $testUser = null;
    private ?CityLocation $testCity = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testUser = User::firstOrCreate(
            ['email' => 'loc.test@rentella.com'],
            [
                'name' => 'Location',
                'surname' => 'Tester',
                'email' => 'loc.test@rentella.com',
                'password' => bcrypt('test123'),
                'uuid' => Str::uuid()->toString(),
            ]
        );

        $this->testCity = CityLocation::firstOrCreate(
            ['city_name' => 'Rimini'],
            ['latitude' => 44.0678, 'longitude' => 12.5695, 'description' => 'Test city']
        );
    }

    // -------------------------------------------------------------------------
    // /api/locations/search
    // -------------------------------------------------------------------------

    /**
     * GET /api/locations/search requires authentication.
     */
    public function test_location_search_without_auth_returns_401(): void
    {
        $response = $this->getJson('/api/locations/search?q=Rimini');
        $response->assertStatus(401);
    }

    /**
     * GET /api/locations/search with empty query returns empty array.
     */
    public function test_location_search_with_empty_query_returns_empty(): void
    {
        $this->actingAs($this->testUser, 'api');
        $response = $this->getJson('/api/locations/search?q=');
        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    /**
     * GET /api/locations/search with query string '0' is not treated as empty.
     */
    public function test_location_search_with_zero_query_is_not_empty(): void
    {
        $this->actingAs($this->testUser, 'api');
        $response = $this->getJson('/api/locations/search?q=0');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    /**
     * GET /api/locations/search happy path returns matching cities.
     */
    public function test_location_search_happy_path_returns_results(): void
    {
        $this->actingAs($this->testUser, 'api');
        $response = $this->getJson('/api/locations/search?q=Rimini&limit=10');
        $response->assertStatus(200);
        $response->assertJsonIsArray();

        $data = $response->json();
        $this->assertNotEmpty($data);

        // Each item should have the expected fields
        foreach ($data as $city) {
            $this->assertArrayHasKey('id', $city);
            $this->assertArrayHasKey('city_name', $city);
            $this->assertArrayHasKey('latitude', $city);
            $this->assertArrayHasKey('longitude', $city);
        }
    }

    /**
     * GET /api/locations/search respects the limit parameter.
     */
    public function test_location_search_respects_limit(): void
    {
        $this->actingAs($this->testUser, 'api');
        $response = $this->getJson('/api/locations/search?q=a&limit=2');
        $response->assertStatus(200);
        $response->assertJsonIsArray();

        $data = $response->json();
        $this->assertLessThanOrEqual(2, count($data));
    }

    // -------------------------------------------------------------------------
    // /api/users/preferred-location
    // -------------------------------------------------------------------------

    /**
     * GET /api/users/preferred-location requires authentication.
     */
    public function test_get_preferred_location_without_auth_returns_401(): void
    {
        $response = $this->getJson('/api/users/preferred-location');
        $response->assertStatus(401);
    }

    /**
     * POST /api/users/preferred-location requires authentication.
     */
    public function test_set_preferred_location_without_auth_returns_401(): void
    {
        $response = $this->postJson('/api/users/preferred-location', [
            'location_id' => $this->testCity->id,
        ]);
        $response->assertStatus(401);
    }

    /**
     * POST /api/users/preferred-location with invalid location_id returns 422.
     */
    public function test_set_preferred_location_with_invalid_id_returns_422(): void
    {
        $this->actingAs($this->testUser, 'api');
        $response = $this->postJson('/api/users/preferred-location', [
            'location_id' => 999999,
        ]);
        $response->assertStatus(422);
    }

    /**
     * POST /api/users/preferred-location happy path sets the location.
     */
    public function test_set_preferred_location_happy_path(): void
    {
        $this->actingAs($this->testUser, 'api');
        $response = $this->postJson('/api/users/preferred-location', [
            'location_id' => $this->testCity->id,
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data']);
    }

    /**
     * GET /api/users/preferred-location happy path returns the preferred location.
     */
    public function test_get_preferred_location_happy_path(): void
    {
        // Ensure a preferred location is set first
        $this->testUser->preferred_location_id = $this->testCity->id;
        $this->testUser->save();

        $this->actingAs($this->testUser, 'api');
        $response = $this->getJson('/api/users/preferred-location');
        $response->assertStatus(200);
        $response->assertJsonStructure(['preferred_location']);
    }

    /**
     * User data returned by the preferred-location endpoint must not expose password.
     */
    public function test_set_preferred_location_response_does_not_expose_password(): void
    {
        $this->actingAs($this->testUser, 'api');
        $response = $this->postJson('/api/users/preferred-location', [
            'location_id' => $this->testCity->id,
        ]);
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertArrayNotHasKey('password', $data ?? []);
        $this->assertArrayNotHasKey('remember_token', $data ?? []);
    }
}
