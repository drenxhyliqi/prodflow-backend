<?php

namespace App\Repositories;

use App\Models\ProductionModel;
use Illuminate\Support\Facades\DB;

class ProductionRepository
{
    protected string $table;

    public function __construct(ProductionModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllProduction(int $limit, int $companyId)
    {
        return DB::table("{$this->table} as pr")
            ->join('products as p', 'pr.product_id', '=', 'p.pid')
            ->join('machines as m', 'pr.machine_id', '=', 'm.mid')
            ->select([
                'pr.pid',
                'pr.product_id',
                'p.product',
                'pr.machine_id',
                'm.machine',
                'pr.qty',
                'pr.date',
                'pr.company_id',
            ])
            ->where('pr.company_id', $companyId)
            ->orderByDesc('pr.pid')
            ->paginate($limit);

    }
    //---------------
    public function getSearchedProduction(string $search, int $limit, int $companyId)
    {
        return DB::table("{$this->table} as pr")
            ->join('products as p', 'pr.product_id', '=', 'p.pid')
            ->join('machines as m', 'pr.machine_id', '=', 'm.mid')
            ->select([
                'pr.pid',
                'pr.product_id',
                'p.product',
                'pr.machine_id',
                'm.machine',
                'pr.qty',
                'pr.date',
                'pr.company_id',
            ])
            ->where(function ($q) use ($search) {
                $q->where('p.product', 'like', "%{$search}%")
                    ->orWhere('m.machine', 'like', "%{$search}%")
                    ->orWhere('pr.qty', 'like', "%{$search}%");
                })
                ->where('pr.company_id', $companyId)
                ->orderByDesc('pr.pid')
                ->paginate($limit);
    }
    //---------------
    public function findProductionById(int $id, int $companyId)
    {
        return DB::table("{$this->table} as pr")
            ->join('products as p', 'pr.product_id', '=', 'p.pid')
            ->join('machines as m', 'pr.machine_id', '=', 'm.mid')
            ->select([
                'pr.pid',
                'pr.product_id',
                'p.product',
                'pr.machine_id',
                'm.machine',
                'pr.qty',
                'pr.date',
                'pr.company_id',
            ])
            ->where('pr.pid', $id)
            ->where('pr.company_id', $companyId)
            ->first();

    }
    //---------------
    public function checkProductionExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)->where('pid', $id)
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
    public function hasDuplicateProduction(
        int $productId,
        int $machineId,
        float $qty,
        string $date,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('product_id', $productId)
            ->where('machine_id', $machineId)
            ->where('qty', $qty)
            ->whereDate('date', $date)
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
    //---------------
    public function checkProductBelongsToCompany(int $productId, int $companyId): bool
    {
        return DB::table('products')
            ->where('pid', $productId)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function checkMachineBelongsToCompany(int $machineId, int $companyId): bool
    {
        return DB::table('machines')
            ->where('mid', $machineId)
            ->where('company_id', $companyId)
            ->exists();
    }
}
