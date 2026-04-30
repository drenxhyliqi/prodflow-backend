<?php

namespace App\Repositories;

use App\Models\ProductsModel;
use Illuminate\Support\Facades\DB;

class ProductsRepository
{
    protected string $table;

    public function __construct(ProductsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllProducts(int $limit, int $companyId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderByDesc('pid')
            ->paginate($limit);    }
    //---------------
    public function getSearchedProducts(string $search, int $limit, int $companyId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('product', 'like', "%{$search}%")
            ->orderByDesc('pid')
            ->paginate($limit);
    }
    //---------------
    public function findProductsById(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('pid', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkProductsExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('pid', $id)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)->insert($data);
    }

    public function hasDuplicateProduct(
        string $product,
        string $unit,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->whereRaw('LOWER(product) = ?', [mb_strtolower(trim($product))])
            ->whereRaw('LOWER(unit) = ?', [mb_strtolower(trim($unit))])
            ->where('pid', '!=', $excludeId)
            ->exists();

    }
    //---------------
    public function update(int $id, array $data, int $companyId): bool
    {
        return DB::table($this->table)->where('pid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)->where('pid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}
