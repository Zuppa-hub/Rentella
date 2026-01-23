<?php

namespace Tests\Feature;

use Tests\TestCase;

class BeachesApiTest extends TestCase
{
    /**
     * Test GET /api/beaches without authentication returns 401
     */
    public function test_beaches_without_token_returns_unauthorized(): void
    {
        $response = $this->getJson('/api/beaches');

        $response->assertStatus(401);
    }

    /**
     * Test GET /api/beaches with valid token returns 200 and data
     */
    public function test_beaches_with_valid_token_returns_success(): void
    {
        // Mock a valid Keycloak token
        $token = $this->getValidKeycloakToken();

        $response = $this->getJson('/api/beaches', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertIsArray();
    }

    /**
     * Helper to get a valid token from Keycloak during tests
     * In production, use test credentials from docker-compose or env
     */
    private function getValidKeycloakToken(): string
    {
        // This would call the Keycloak token endpoint with test credentials
        // For now, we'll use a placeholder; in CI this should be real
        $keycloakUrl = env('KEYCLOAK_BASE_URL', 'http://keycloak:8080');
        $realm = env('KEYCLOAK_REALM', 'rentella');
        $clientId = env('KEYCLOAK_CLIENT_ID', 'rentella-api');

        // Call token endpoint
        $response = \Http::post("{$keycloakUrl}/realms/{$realm}/protocol/openid-connect/token", [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'username' => 'testuser', // From seeder
            'password' => 'test123',  // Keycloak test user password
        ]);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        $this->fail('Could not obtain test token from Keycloak');
    }
}
