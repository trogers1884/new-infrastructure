<?php
// tests/Feature/Components/Api/ApiTestCase.php

namespace Tests\Feature\Components\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Feature\Components\Api\Traits\HandlesCustomSchemas;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase, HandlesCustomSchemas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runCustomMigrations();
    }
    /**
     * Create an authenticated user for testing.
     *
     * @param array $attributes
     * @return User
     */
    protected function createAuthenticatedUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        Sanctum::actingAs($user);
        return $user;
    }

    /**
     * Get common headers for API requests.
     *
     * @param string $version
     * @return array
     */
    protected function getHeaders(string $version = 'v1'): array
    {
        return [
            'Accept' => 'application/json',
            'X-API-Version' => $version,
        ];
    }

    /**
     * Assert API response structure is valid.
     *
     * @param array $response
     * @return void
     */
    protected function assertValidApiResponse(array $response): void
    {
        $this->assertArrayHasKey('success', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertArrayHasKey('timestamp', $response['meta']);
        $this->assertArrayHasKey('request_id', $response['meta']);
        $this->assertArrayHasKey('api_version', $response['meta']);
    }
}
