<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Beach;
use App\Models\Owner;
use App\Models\CityLocation;
use App\Models\OpeningDate;
use App\Models\BeachType;
use App\Models\User;

class BeachesApiTest extends TestCase
{
    private ?User $testUser = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create or get test user for mocking auth
        $this->testUser = User::firstOrCreate(
            ['email' => 'test@rentella.com'],
            [
                'name' => 'Test User',
                'email' => 'test@rentella.com',
                'password' => bcrypt('test123'),
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            ]
        );
    }

    /**
     * Mock the Keycloak auth guard by setting authenticated user
     */
    private function actAsAuthenticatedUser(): void
    {
        // Mock auth by setting the user for the request
        $this->actingAs($this->testUser, 'api');
    }

    /**
     * Test GET /api/beaches without authentication returns 401
     */
    public function test_beaches_without_token_returns_unauthorized(): void
    {
        $response = $this->getJson('/api/beaches');
        $response->assertStatus(401);
    }

    /**
     * Test GET /api/beaches with authenticated user returns 200 and data
     */
    public function test_beaches_with_authenticated_user_returns_success(): void
    {
        $this->actAsAuthenticatedUser();
        
        $response = $this->getJson('/api/beaches');
        $response->assertStatus(200);
        $response->assertIsArray();
    }

    /**
     * Test POST /api/beaches creates a new beach
     */
    public function test_create_beach_with_valid_data(): void
    {
        $this->actAsAuthenticatedUser();
        
        $owner = Owner::firstOrCreate(['name' => 'Test Owner', 'fiscal_code' => 'OWNERRR11111111']);
        $location = CityLocation::firstOrCreate(['city_name' => 'Test City', 'country' => 'Italy']);
        $openingDate = OpeningDate::firstOrCreate(['opening_date' => '2024-01-01', 'closing_date' => '2024-12-31']);
        $beachType = BeachType::firstOrCreate(['type_name' => 'Sandy']);

        $beachData = [
            'owner_id' => $owner->id,
            'name' => 'New Beach Test',
            'location_id' => $location->id,
            'description' => 'A beautiful test beach',
            'opening_date_id' => $openingDate->id,
            'special_note' => 'Testing beach creation',
            'latitude' => 45.5,
            'longitude' => 12.5,
            'allowed_animals' => true,
            'type_id' => $beachType->id,
        ];

        $response = $this->postJson('/api/beaches', $beachData);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'name', 'owner_id', 'latitude', 'longitude']);
        $this->assertDatabaseHas('beaches', ['name' => 'New Beach Test']);
    }

    /**
     * Test POST /api/beaches fails with invalid data
     */
    public function test_create_beach_with_invalid_data(): void
    {
        $this->actAsAuthenticatedUser();
        
        $invalidData = [
            'owner_id' => 9999, // Non-existent owner
            'name' => 'A', // Too short
            'location_id' => 9999,
            'latitude' => 91, // Out of range
            'longitude' => 200,
        ];

        $response = $this->postJson('/api/beaches', $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['owner_id', 'name', 'location_id', 'latitude', 'longitude']);
    }

    /**
     * Test GET /api/beaches/{id} returns a specific beach
     */
    public function test_show_beach_returns_detail(): void
    {
        $this->actAsAuthenticatedUser();
        
        $beach = Beach::firstOrCreate(
            ['name' => 'Existing Beach'],
            [
                'owner_id' => Owner::firstOrCreate(['name' => 'Owner', 'fiscal_code' => 'OWNER11111111'])->id,
                'location_id' => CityLocation::firstOrCreate(['city_name' => 'City', 'country' => 'Italy'])->id,
                'opening_date_id' => OpeningDate::firstOrCreate(['opening_date' => '2024-01-01', 'closing_date' => '2024-12-31'])->id,
                'latitude' => 45.5,
                'longitude' => 12.5,
                'allowed_animals' => false,
                'type_id' => BeachType::firstOrCreate(['type_name' => 'Rocky'])->id,
            ]
        );

        $response = $this->getJson("/api/beaches/{$beach->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('id', $beach->id);
        $response->assertJsonPath('name', $beach->name);
    }

    /**
     * Test PUT /api/beaches/{id} updates a beach
     */
    public function test_update_beach_with_valid_data(): void
    {
        $this->actAsAuthenticatedUser();
        
        $beach = Beach::firstOrCreate(
            ['name' => 'Beach to Update'],
            [
                'owner_id' => Owner::firstOrCreate(['name' => 'Owner', 'fiscal_code' => 'OWNER11111111'])->id,
                'location_id' => CityLocation::firstOrCreate(['city_name' => 'City', 'country' => 'Italy'])->id,
                'opening_date_id' => OpeningDate::firstOrCreate(['opening_date' => '2024-01-01', 'closing_date' => '2024-12-31'])->id,
                'latitude' => 45.5,
                'longitude' => 12.5,
                'allowed_animals' => false,
                'type_id' => BeachType::firstOrCreate(['type_name' => 'Rocky'])->id,
            ]
        );

        $updateData = [
            'name' => 'Updated Beach Name',
            'description' => 'Updated description',
            'allowed_animals' => true,
        ];

        $response = $this->putJson("/api/beaches/{$beach->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('name', 'Updated Beach Name');
        $this->assertDatabaseHas('beaches', ['id' => $beach->id, 'name' => 'Updated Beach Name']);
    }

    /**
     * Test DELETE /api/beaches/{id} deletes a beach
     */
    public function test_delete_beach(): void
    {
        $this->actAsAuthenticatedUser();
        
        $beach = Beach::create([
            'owner_id' => Owner::firstOrCreate(['name' => 'Owner', 'fiscal_code' => 'OWNER11111111'])->id,
            'location_id' => CityLocation::firstOrCreate(['city_name' => 'City', 'country' => 'Italy'])->id,
            'opening_date_id' => OpeningDate::firstOrCreate(['opening_date' => '2024-01-01', 'closing_date' => '2024-12-31'])->id,
            'name' => 'Beach to Delete',
            'latitude' => 45.5,
            'longitude' => 12.5,
            'allowed_animals' => false,
            'type_id' => BeachType::firstOrCreate(['type_name' => 'Rocky'])->id,
        ]);

        $response = $this->deleteJson("/api/beaches/{$beach->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('beaches', ['id' => $beach->id]);
    }

    /**
     * Test DELETE non-existent beach returns 404
     */
    public function test_delete_nonexistent_beach_returns_404(): void
    {
        $this->actAsAuthenticatedUser();
        
        $response = $this->deleteJson('/api/beaches/9999');

        $response->assertStatus(404);
    }
}
