<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BeachType;
use App\Models\CityLocation;
use App\Models\BeachZone;
use App\Models\Umbrella;
use App\Models\OpeningDate;
use App\Models\Owner;

class ApiHealthCheckTest extends TestCase
{
    private ?User $testUser = null;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->testUser = User::firstOrCreate(
            ['email' => 'test.health@rentella.com'],
            [
                'name' => 'Test Health',
                'surname' => 'Check',
                'email' => 'test.health@rentella.com',
                'password' => bcrypt('test123'),
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            ]
        );
    }

    private function actAsAuthenticatedUser(): void
    {
        $this->actingAs($this->testUser, 'api');
    }

    public function test_beach_types_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        // Create a sample beach type
        BeachType::firstOrCreate(['type' => 'Sandy']);
        
        $response = $this->getJson('/api/beach-types');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_locations_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        // Create a sample location
        CityLocation::firstOrCreate(
            ['city_name' => 'Test City'],
            ['latitude' => 43.7, 'longitude' => 10.4, 'description' => 'Test City']
        );
        
        $response = $this->getJson('/api/locations');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_beach_zones_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        $response = $this->getJson('/api/beach-zones');
        // Beach zones may be empty, but endpoint should return 200
        $response->assertStatus(200);
    }

    public function test_umbrellas_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        $response = $this->getJson('/api/umbrellas');
        // Umbrellas may be empty, but endpoint should return 200
        $response->assertStatus(200);
    }

    public function test_opening_dates_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        // Create a sample opening date
        OpeningDate::firstOrCreate(['start_date' => '2024-01-01', 'end_date' => '2024-12-31']);
        
        $response = $this->getJson('/api/opening-dates');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_owners_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        // Create a sample owner
        Owner::firstOrCreate(
            ['name' => 'Test Owner', 'surname' => 'Test'],
            ['email' => 'owner.health@test.com', 'adress' => 'Via Test', 'phone_number' => '1234567890']
        );
        
        $response = $this->getJson('/api/owners');
        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }

    public function test_users_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        $response = $this->getJson('/api/users');
        $response->assertStatus(200);
        // GET /api/users now returns only the authenticated user (not array)
        $data = $response->json();
        $this->assertEquals($this->testUser->email, $data['email']);
    }

    public function test_orders_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        $response = $this->getJson('/api/orders');
        // Orders may be empty, but endpoint should return 200
        $response->assertStatus(200);
    }

    public function test_prices_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        $response = $this->getJson('/api/prices');
        // Prices may be empty, but endpoint should return 200
        $response->assertStatus(200);
    }

    public function test_beach_pictures_endpoint(): void
    {
        $this->actAsAuthenticatedUser();
        
        $response = $this->getJson('/api/beach-pictures');
        // Pictures may be empty, but endpoint should return 200
        $response->assertStatus(200);
    }
}
