<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\SeedsApiFixtures;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;
    use SeedsApiFixtures;

    public function test_login_returns_token_for_valid_credentials(): void
    {
        $user = $this->seedUser(['username' => 'admin.test']);

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'password' => self::DEFAULT_PASSWORD,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => ['uid', 'user', 'username', 'company_id', 'role'],
            ]);
    }

    public function test_login_returns_unauthorized_for_wrong_credentials(): void
    {
        $user = $this->seedUser(['username' => 'admin.test']);

        $response = $this->postJson('/api/login', [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'Wrong Credentials.',
            ]);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = $this->seedUser(['username' => 'manager.test']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertOk()
            ->assertJson([
                'uid' => $user->uid,
                'username' => $user->username,
            ]);
    }
}
