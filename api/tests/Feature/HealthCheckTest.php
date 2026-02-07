<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test GET /api/health returns 200 (public endpoint)
     */
    public function test_health_check_is_public(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }
}
