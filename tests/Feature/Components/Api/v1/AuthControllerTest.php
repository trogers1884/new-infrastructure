<?php

namespace Tests\Feature\Components\Api\v1;

use App\Models\User;
use Tests\Feature\Components\Api\ApiTestCase;

class AuthControllerTest extends ApiTestCase
{
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            'device_name' => 'test_device',
        ], $this->getHeaders());

        $response->assertStatus(200);
        $this->assertValidApiResponse($response->json());
    }
}
