<?php

namespace Tests\Concerns;

use App\Models\UsersModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait SeedsApiFixtures
{
    protected const DEFAULT_PASSWORD = 'password123';

    protected function seedCompany(array $overrides = []): int
    {
        return DB::table('companies')->insertGetId(array_merge([
            'name' => 'Test Company',
            'sector' => 'Manufacturing',
            'location' => 'Prishtina',
            'status' => 'Active',
        ], $overrides), 'cid');
    }

    protected function seedUser(array $overrides = []): UsersModel
    {
        if (isset($overrides['company_id'])) {
            $companyId = $overrides['company_id'];
        } else {
            $companyId = $this->seedCompany();
        }

        $userId = DB::table('users')->insertGetId([
            'user' => $overrides['user'] ?? 'Test User',
            'username' => $overrides['username'] ?? 'test.user.' . uniqid(),
            'password' => $overrides['password'] ?? Hash::make(self::DEFAULT_PASSWORD),
            'company_id' => $companyId,
            'role' => $overrides['role'] ?? 'manager',
        ], 'uid');

        return UsersModel::findOrFail($userId);
    }

    protected function seedClient(int $companyId, array $overrides = []): int
    {
        return DB::table('clients')->insertGetId(array_merge([
            'client' => 'Test Client',
            'phone' => '044111222',
            'location' => 'Prishtina',
            'company_id' => $companyId,
        ], $overrides), 'cid');
    }

    protected function seedMachine(int $companyId, array $overrides = []): int
    {
        return DB::table('machines')->insertGetId(array_merge([
            'machine' => 'CNC-01',
            'type' => 'CNC',
            'company_id' => $companyId,
        ], $overrides), 'mid');
    }

    protected function seedWarehouse(int $companyId, array $overrides = []): int
    {
        return DB::table('warehouses')->insertGetId(array_merge([
            'warehouse' => 'Main Warehouse',
            'location' => 'Zone A',
            'capacity' => 1000,
            'company_id' => $companyId,
        ], $overrides), 'wid');
    }

    protected function seedMaterial(int $companyId, array $overrides = []): int
    {
        return DB::table('materials')->insertGetId(array_merge([
            'material' => 'Steel sheet',
            'unit' => 'kg',
            'company_id' => $companyId,
        ], $overrides), 'mid');
    }

    protected function seedMaterialsStock(int $companyId, int $materialId, int $warehouseId, array $overrides = []): int
    {
        return DB::table('materials_stock')->insertGetId(array_merge([
            'material_id' => $materialId,
            'type' => 'in',
            'qty' => 100,
            'date' => '2026-05-01',
            'warehouse_id' => $warehouseId,
            'company_id' => $companyId,
        ], $overrides), 'msid');
    }

    protected function seedExpense(int $companyId, array $overrides = []): int
    {
        return DB::table('expenses')->insertGetId(array_merge([
            'comment' => 'Office supplies',
            'price' => 150.50,
            'date' => '2026-05-15',
            'company_id' => $companyId,
        ], $overrides), 'eid');
    }
}
