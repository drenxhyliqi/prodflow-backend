<?php

namespace App\Repositories;

use App\Models\SuppliersModel;
use Illuminate\Support\Facades\DB;

class SuppliersRepository
{
    protected string $table;
    public function __construct(SuppliersModel $model)
    {
        $this->table = $model->getTable();

    }
    //---------------
    public function getAllSuppliers(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderByDesc('sid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedSuppliers(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('supplier', 'like', "%{$search}%")
            ->orderByDesc('sid')
            ->paginate($limit);
    }
    //---------------
    public function findSupplier(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkSupplierExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function create(array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->insert(array_merge($data, [
                'company_id' => $companyId,
            ]));
    }
    //---------------
    public function update(int $id, array $data, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}
