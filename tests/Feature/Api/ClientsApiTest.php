<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\Concerns\SeedsApiFixtures;
use Tests\TestCase;

class ClientsApiTest extends TestCase
{
    use RefreshDatabase;
    use SeedsApiFixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $taggedCache = Mockery::mock();
        $taggedCache->shouldReceive('flush')->andReturn(true);

        Cache::partialMock()
            ->shouldReceive('tags')
            ->with(['clients'])
            ->andReturn($taggedCache);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_clients_requires_authentication(): void
    {
        $response = $this->getJson('/api/admin/clients');

        $response->assertUnauthorized();
    }

    public function test_manager_can_list_clients(): void
    {
        $user = $this->seedUser();
        $this->seedClient($user->company_id, ['client' => 'Alpha Corp']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/clients');

        $response->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'per_page', 'total']);
    }

    public function test_manager_can_create_client(): void
    {
        $user = $this->seedUser();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/admin/create_client', [
            'client' => 'New Client',
            'phone' => '044123456',
            'location' => 'Prishtina',
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Client registered successfully.',
            ]);

        $this->assertDatabaseHas('clients', [
            'client' => 'New Client',
            'company_id' => $user->company_id,
        ]);
    }

    public function test_create_client_fails_validation_when_fields_missing(): void
    {
        $user = $this->seedUser();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/admin/create_client', []);

        $response->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
            ])
            ->assertJsonStructure(['errors']);
    }

    public function test_manager_can_get_client_by_id(): void
    {
        $user = $this->seedUser();
        $clientId = $this->seedClient($user->company_id, [
            'client' => 'Beta Corp',
            'phone' => '044999888',
            'location' => 'Peja',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/admin/edit_client/{$clientId}");

        $response->assertOk()
            ->assertJson([
                'cid' => $clientId,
                'client' => 'Beta Corp',
                'phone' => '044999888',
                'location' => 'Peja',
            ]);
    }
}
