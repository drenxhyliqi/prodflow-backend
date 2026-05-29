<?php

namespace Tests\Feature\Api;

use App\Models\UsersModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private const DEFAULT_PASSWORD = 'password123';

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

    private function seedUser(array $overrides = []): UsersModel
    {
        $companyId = $overrides['company_id'] ?? $this->seedCompany();

        $userId = DB::table('users')->insertGetId(array_merge([
            'user' => 'Test User',
            'username' => 'test.user',
            'password' => Hash::make(self::DEFAULT_PASSWORD),
            'company_id' => $companyId,
            'role' => 'manager',
        ], $overrides), 'uid');

        return UsersModel::findOrFail($userId);
    }

    private function seedCompany(): int
    {
        return DB::table('companies')->insertGetId([
            'name' => 'Test Company',
            'sector' => 'Manufacturing',
            'location' => 'Prishtina',
            'status' => 'Active',
        ], 'cid');
    }
}
