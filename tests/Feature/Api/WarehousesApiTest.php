<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\SeedsApiFixtures;
use Tests\TestCase;

class WarehousesApiTest extends TestCase
{
    use RefreshDatabase;
    use SeedsApiFixtures;

    public function test_list_exposes_used_capacity_from_stock_movements(): void
    {
        $user = $this->seedUser();
        $warehouseId = $this->seedWarehouse($user->company_id, ['capacity' => 1000]);
        $materialId = $this->seedMaterial($user->company_id);
        $this->seedMaterialsStock($user->company_id, $materialId, $warehouseId, [
            'type' => 'in',
            'qty' => 350,
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/warehouses');

        $response->assertOk();
        $row = collect($response->json('data'))->firstWhere('wid', $warehouseId);
        $this->assertNotNull($row);
        $this->assertEquals(350, (float) $row['used_capacity']);
    }

    public function test_update_rejects_capacity_below_current_usage(): void
    {
        $user = $this->seedUser();
        $warehouseId = $this->seedWarehouse($user->company_id, [
            'warehouse' => 'Heavy Store',
            'location' => 'Bay 1',
            'capacity' => 1000,
        ]);
        $materialId = $this->seedMaterial($user->company_id);
        $this->seedMaterialsStock($user->company_id, $materialId, $warehouseId, [
            'type' => 'in',
            'qty' => 600,
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/admin/update_warehouse', [
            'wid' => $warehouseId,
            'warehouse' => 'Heavy Store',
            'location' => 'Bay 1',
            'capacity' => 400,
        ]);

        $response->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'Cannot reduce capacity below current usage. Warehouse is using 600 units.',
            ]);
    }

    public function test_search_returns_only_matching_warehouses(): void
    {
        $user = $this->seedUser();
        $this->seedWarehouse($user->company_id, ['warehouse' => 'Alpha Depot', 'location' => 'North']);
        $this->seedWarehouse($user->company_id, ['warehouse' => 'Beta Storage', 'location' => 'South']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/admin/warehouses?search=Alpha');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('warehouse')->all();
        $this->assertContains('Alpha Depot', $names);
        $this->assertNotContains('Beta Storage', $names);
    }
}
