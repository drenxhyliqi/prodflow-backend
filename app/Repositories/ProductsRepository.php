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

    public function getAllProducts(int $limit, ?int $companyId = null)
    {
        $query = DB::table($this->table)->orderByDesc('pid');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->paginate($limit);
    }

    public function getSearchedProducts(string $search, int $limit, ?int $companyId = null)
    {
        $query = DB::table($this->table)
            ->where(function ($q) use ($search) {
                $q->where('product', 'like', "%{$search}%");
            })
            ->orderByDesc('pid');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->paginate($limit);
    }

    public function findProductsById(int $id, ?int $companyId = null)
    {
        $query = DB::table($this->table)->where('pid', $id);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->first();
    }

    public function checkProductsExist(int $id, ?int $companyId = null): bool
    {
        $query = DB::table($this->table)->where('pid', $id);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->exists();
    }

    public function create(array $data): bool
    {
        return DB::table($this->table)->insert($data);
    }

    public function update(int $id, array $data, ?int $companyId = null): bool
    {
        $query = DB::table($this->table)->where('pid', $id);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->update($data) > 0;
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        $query = DB::table($this->table)->where('pid', $id);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return (bool) $query->delete();
    }
}
