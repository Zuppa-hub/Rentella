<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DeepSecurityAuditTest extends TestCase
{
    private $user1;
    private $user2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create two different users
        $this->user1 = User::create([
            'email' => 'owner1@test.com',
            'name' => 'Owner',
            'surname' => 'One',
            'uuid' => 'uuid-owner-1',
            'password' => bcrypt('test123'),
        ]);

        $this->user2 = User::create([
            'email' => 'attacker@test.com',
            'name' => 'Attacker',
            'surname' => 'User',
            'uuid' => 'uuid-attacker-1',
            'password' => bcrypt('test123'),
        ]);
    }

    // ============ OWNERS CONTROLLER - CRITICAL VULNERABILITIES ============

    public function test_attacker_cannot_list_all_owners()
    {
        // Vulnerable: anyone can list all owners
        $response = $this->actingAs($this->user2, 'api')
            ->getJson('/api/owners');

        // Expected: 403 Forbidden (or restricted access)
        // Actual: 200 OK with all owners (VULNERABLE!)
        $this->assertEquals(403, $response->status(), 
            'VULNERABILITY: OwnersController allows listing all owners without authorization');
    }

    public function test_attacker_cannot_create_owner()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->postJson('/api/owners', [
                'name' => 'Fake',
                'surname' => 'Owner',
                'email' => 'fake@test.com',
                'adress' => 'Via Fake',
                'phone_number' => '123',
            ]);

        $this->assertEquals(403, $response->status(),
            'VULNERABILITY: OwnersController allows creating owners without authorization');
    }

    // ============ BEACH PICTURES CONTROLLER - CRITICAL VULNERABILITIES ============

    public function test_cannot_add_pictures_without_beach_ownership()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->postJson('/api/beach-pictures', [
                'beach_id' => 999,
                'photo' => 'test.jpg',
            ]);

        $this->assertTrue(in_array($response->status(), [403, 404, 422]),
            'VULNERABILITY: BeachPictureController allows adding pictures to any beach');
    }

    public function test_cannot_delete_beach_pictures_without_ownership()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->deleteJson('/api/beach-pictures/999');

        $this->assertTrue(in_array($response->status(), [403, 404, 422]),
            'VULNERABILITY: BeachPictureController allows deleting any picture');
    }

    // ============ BEACH ZONES CONTROLLER - CRITICAL VULNERABILITIES ============

    public function test_cannot_add_zones_without_beach_ownership()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->postJson('/api/beach-zones', [
                'beach_id' => 999,
                'name' => 'Fake Zone',
                'price_id' => 1,
                'description' => 'test',
                'special_note' => 'test',
                'latitude' => 43.0,
                'longitude' => 12.0,
            ]);

        $this->assertTrue(in_array($response->status(), [403, 404, 422]),
            'VULNERABILITY: BeachZonesController allows adding zones to any beach');
    }

    public function test_cannot_edit_zones_without_beach_ownership()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->putJson('/api/beach-zones/999', [
                'name' => 'Hacked Zone',
            ]);

        $this->assertTrue(in_array($response->status(), [403, 404, 422]),
            'VULNERABILITY: BeachZonesController allows editing zones without ownership check');
    }

    // ============ UMBRELLAS CONTROLLER - CRITICAL VULNERABILITIES ============

    public function test_cannot_add_umbrellas_without_beach_ownership()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->postJson('/api/umbrellas', [
                'beach_zone_id' => 999,
                'number' => 5,
                'special_note' => 'test',
            ]);

        $this->assertTrue(in_array($response->status(), [403, 404, 422]),
            'VULNERABILITY: UmbrellasController allows adding umbrellas without ownership check');
    }

    // ============ OPENING DATES CONTROLLER - CRITICAL VULNERABILITIES ============

    public function test_cannot_edit_opening_dates_without_beach_ownership()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->putJson('/api/opening-dates/999', [
                'opening_date' => '2024-12-01',
            ]);

        $this->assertTrue(in_array($response->status(), [403, 404, 422]),
            'VULNERABILITY: OpeningDatesController allows editing without ownership check');
    }

    // ============ PRICES CONTROLLER - CRITICAL VULNERABILITIES ============

    public function test_cannot_edit_prices_without_beach_ownership()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->putJson('/api/prices/999', [
                'price' => 5,
            ]);

        $this->assertTrue(in_array($response->status(), [403, 404, 422]),
            'VULNERABILITY: PricesController allows editing prices without ownership check');
    }

    // ============ BEACH TYPE CONTROLLER - CRITICAL VULNERABILITIES ============

    public function test_cannot_create_beach_types()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->postJson('/api/beach-types', [
                'type' => 'sand',
            ]);

        $this->assertEquals(403, $response->status(),
            'VULNERABILITY: BeachTypeController allows creating types without authorization');
    }

    // ============ LOCATION CONTROLLER - CRITICAL VULNERABILITIES ============

    public function test_cannot_create_locations()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->postJson('/api/locations', [
                'city_name' => 'Fake City',
                'latitude' => 43.0,
                'longitude' => 12.0,
                'description' => 'test',
            ]);

        $this->assertEquals(403, $response->status(),
            'VULNERABILITY: LocationController allows creating locations without authorization');
    }

    public function test_cannot_edit_locations()
    {
        $response = $this->actingAs($this->user2, 'api')
            ->putJson('/api/locations/999', [
                'city_name' => 'Hacked City',
                'latitude' => 50.0,
                'longitude' => 50.0,
            ]);

        $this->assertTrue(in_array($response->status(), [403, 404, 422]),
            'VULNERABILITY: LocationController allows editing locations without authorization');
    }
}
