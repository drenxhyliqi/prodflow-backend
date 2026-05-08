<?php

namespace App\Repositories;

use App\Models\ContractsModel;
use Illuminate\Support\Facades\DB;

class ContractsRepository
{
    protected string $table;
    public function __construct(ContractsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getContracts($limit)
    {
        return DB::table($this->table)
            ->orderByDesc('cid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedContracts(string $search, int $limit)
    {
        return DB::table($this->table)
            ->where('status', 'like', "%{$search}%") 
            ->orderByDesc('cid')
            ->paginate($limit);
    }
    //---------------
    public function getAllContracts()
    {
        return DB::table($this->table)
            ->orderByDesc('cid')
            ->get();
    }
    //---------------
    public function findContract(int $id)
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->first();
    }
    //---------------
    public function checkContractExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->exists();
    }
    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
    }
    //---------------
    public function update(int $id, array $data): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->update($data) > 0;
    }
    //---------------
    public function changeStatus(int $id, string $status): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->update([
                'status' => $status
            ]) > 0;
    }
    //---------------
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->delete() > 0;
    }
}