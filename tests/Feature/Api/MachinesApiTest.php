<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\SeedsApiFixtures;
use Tests\TestCase;

class MachinesApiTest extends TestCase
{
    use RefreshDatabase;
    use SeedsApiFixtures;

    public function test_machines_requires_authentication(): void
    {
        $this->getJson('/api/admin/machines')->assertUnauthorized();
    }

    public function test_manager_can_list_machines(): void
    {
        $user = $this->seedUser();
        $this->seedMachine($user->company_id, ['machine' => 'Press-01', 'type' => 'Hydraulic']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/machines');

        $response->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'per_page', 'total']);
    }

    public function test_manager_can_create_machine(): void
    {
        $user = $this->seedUser();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/admin/create_machine', [
            'machine' => 'Lathe-02',
            'type' => 'Lathe',
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Machine registered successfully.',
            ]);

        $this->assertDatabaseHas('machines', [
            'machine' => 'Lathe-02',
            'type' => 'Lathe',
            'company_id' => $user->company_id,
        ]);
    }

    public function test_create_machine_fails_validation_when_fields_missing(): void
    {
        $user = $this->seedUser();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/admin/create_machine', []);

        $response->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
            ])
            ->assertJsonStructure(['errors']);
    }

    public function test_manager_can_get_machine_by_id(): void
    {
        $user = $this->seedUser();
        $machineId = $this->seedMachine($user->company_id, [
            'machine' => 'Mill-03',
            'type' => 'Milling',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/admin/edit_machine/{$machineId}");

        $response->assertOk()
            ->assertJson([
                'mid' => $machineId,
                'machine' => 'Mill-03',
                'type' => 'Milling',
            ]);
    }

    public function test_manager_can_update_machine(): void
    {
        $user = $this->seedUser();
        $machineId = $this->seedMachine($user->company_id);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/admin/update_machine', [
            'mid' => $machineId,
            'machine' => 'CNC-Updated',
            'type' => 'CNC Pro',
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'The machine was successfully updated.',
            ]);

        $this->assertDatabaseHas('machines', [
            'mid' => $machineId,
            'machine' => 'CNC-Updated',
            'type' => 'CNC Pro',
        ]);
    }

    public function test_manager_can_delete_machine(): void
    {
        $user = $this->seedUser();
        $machineId = $this->seedMachine($user->company_id);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/admin/delete_machine/{$machineId}");

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'The machine was successfully deleted.',
            ]);

        $this->assertDatabaseMissing('machines', ['mid' => $machineId]);
    }

    public function test_edit_machine_returns_not_found_for_other_company(): void
    {
        $user = $this->seedUser();
        $otherCompanyId = $this->seedCompany(['name' => 'Other Co']);
        $foreignMachineId = $this->seedMachine($otherCompanyId);
        Sanctum::actingAs($user);

        $this->getJson("/api/admin/edit_machine/{$foreignMachineId}")
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Machine not found.',
            ]);
    }
}
