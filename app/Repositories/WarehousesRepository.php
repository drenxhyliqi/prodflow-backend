<?php

namespace App\Repositories;

use App\Models\WarehousesModel;
use Illuminate\Support\Facades\DB;

class WarehousesRepository
{
    protected string $table;

    public function __construct(WarehousesModel $model)
    {
        $this->table = $model->getTable();
    }

    private function withUsedCapacity(): \Illuminate\Database\Query\Builder
    {
        return DB::table($this->table)
            ->selectRaw("
                warehouses.*,
                COALESCE((
                    SELECT SUM(CASE WHEN type = 'in' THEN qty ELSE -qty END)
                    FROM materials_stock
                    WHERE materials_stock.warehouse_id = warehouses.wid
                ), 0) as used_capacity
            ");
    }

    public function getAllWarehouses(int $companyId, int $limit)
    {
        return $this->withUsedCapacity()
            ->where('warehouses.company_id', $companyId)
            ->orderByDesc('warehouses.wid')
            ->paginate($limit);
    }

    public function getSearchedWarehouses(int $companyId, int $limit, string $search)
    {
        return $this->withUsedCapacity()
            ->where('warehouses.company_id', $companyId)
            ->where(function ($q) use ($search) {
                $q->where('warehouses.warehouse', 'like', "%{$search}%")
                  ->orWhere('warehouses.location', 'like', "%{$search}%");
            })
            ->orderByDesc('warehouses.wid')
            ->paginate($limit);
    }

    public function findWarehouse(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('wid', $id)
            ->where('company_id', $companyId)
            ->first();
    }

    public function checkWarehouseExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('wid', $id)
            ->where('company_id', $companyId)
            ->exists();
    }

    public function create(array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->insert(array_merge($data, [
                'company_id' => $companyId,
            ]));
    }

    public function update(int $id, array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('wid', $id)
            ->where('company_id', $companyId)
            ->update($data) >= 0;
    }

    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('wid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}