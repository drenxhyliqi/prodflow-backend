<?php

namespace App\Repositories;

use App\Models\SalesModel;
use Illuminate\Support\Facades\DB;

class SalesRepository
{
    protected string $table;
    public function __construct(SalesModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllSales($limit)
    {
        return DB::table($this->table)
            ->orderByDesc('sid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedSales($search, $limit)
    {
        return DB::table($this->table)
            ->where('client_id', 'like', "%{$search}%")
            ->orderByDesc('sid')
            ->paginate($limit);
    }
    //---------------
    public function findSale(int $id)
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->first();
    }
    //---------------
    public function checkSaleExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
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
            ->where('sid', $id)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->delete() > 0;
    }
}
