<?php

namespace App\Repositories;

use App\Models\PlanificationModel;
use Illuminate\Support\Facades\DB;

class PlanificationRepository
{
    protected string $table;

    public function __construct(PlanificationModel $model)
    {
        $this->table = $model->getTable();
    }
    public function getAllPlanification(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->join('products', 'planification.product_id', '=', 'products.pid')
            ->select('planification.*', 'products.product as product_name')
            ->where('planification.company_id', $companyId)
            ->orderByDesc('planification.pid')
            ->paginate($limit);
    }
    public function findPlanification(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->join('products', 'planification.product_id', '=', 'products.pid')
            ->select('planification.*', 'products.product as product_name')
            ->where('planification.pid', $id)
            ->where('planification.company_id', $companyId)
            ->first();
    }
    public function checkPlanificationExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('pid', $id)
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
            ->where('pid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('pid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}
