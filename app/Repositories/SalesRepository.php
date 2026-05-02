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
    public function getAllSales(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->select('sale_number', 'client', 'date')
            ->where('company_id', $companyId)
            ->groupBy('sale_number', 'client', 'date')
            ->orderByDesc('date')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedSales(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->select('sale_number', 'client', 'date')
            ->where('company_id', $companyId)
            ->where('sale_number', 'like', "%{$search}%")
            ->groupBy('sale_number', 'client', 'date')
            ->orderByDesc('date')
            ->paginate($limit);
    }
    //---------------
    public function findSale(string $sale_number, int $companyId)
    {
        return DB::table($this->table)
            ->where('sale_number', $sale_number)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkSaleExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
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
    public function delete(string $sale_number, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sale_number', $sale_number)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}
