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

    public function getAllWarehouses(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderByDesc('wid')
            ->paginate($limit);
    }

    public function getSearchedWarehouses(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('warehouse', 'like', "%{$search}%")
            ->orWhere('location', 'like', "%{$search}%")
            ->orderByDesc('wid')
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