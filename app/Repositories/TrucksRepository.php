<?php

namespace App\Repositories;

use App\Models\TrucksModel;
use Illuminate\Support\Facades\DB;

class TrucksRepository
{
    protected string $table;
    public function __construct(TrucksModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllTrucks(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderByDesc('tid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedTrucks(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('truck', 'like', "%{$search}%")
            ->orderByDesc('tid')
            ->paginate($limit);
    }
    //---------------
    public function findTruck(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('tid', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkTruckExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('tid', $id)
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
            ->where('tid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('tid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
    //---------------
    public function changeStatus(int $id, int $companyId, string $newStatus): bool
    {
        return DB::table($this->table)
            ->where('tid', $id)
            ->where('company_id', $companyId)
            ->update(['status' => $newStatus]) > 0;
    }
}
