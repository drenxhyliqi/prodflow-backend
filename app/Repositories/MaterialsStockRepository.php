<?php

namespace App\Repositories;

use App\Models\MaterialsStockModel;
use Illuminate\Support\Facades\DB;

class MaterialsStockRepository
{
    protected string $table;

    public function __construct(MaterialsStockModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllMaterialsStock(int $limit, int $companyId)
    {
        return DB::table("{$this->table} as ms")
            ->leftJoin('materials as mat', 'ms.material_id', '=', 'mat.mid')
            ->leftJoin('warehouses as wh', 'ms.warehouse_id', '=', 'wh.wid')
            ->select([
                'ms.msid',
                'ms.material_id',
                'mat.material',
                'mat.unit',
                'ms.type',
                'ms.qty',
                'ms.date',
                'ms.warehouse_id',
                'wh.warehouse',
                'ms.company_id',
            ])
            ->orderByDesc('ms.msid')
            ->where('ms.company_id', $companyId)
            ->paginate($limit);
    }
    //---------------
    public function getSearchedMaterialsStock(string $search, int $limit, int $companyId)
    {
        return DB::table("{$this->table} as ms")
            ->leftJoin('materials as mat', 'ms.material_id', '=', 'mat.mid')
            ->leftJoin('warehouses as wh', 'ms.warehouse_id', '=', 'wh.wid')
            ->select([
                'ms.msid',
                'ms.material_id',
                'mat.material',
                'mat.unit',
                'ms.type',
                'ms.qty',
                'ms.date',
                'ms.warehouse_id',
                'wh.warehouse',
                'ms.company_id',
            ])
            ->where(function ($q) use ($search) {
                $q->where('mat.material', 'like', "%{$search}%")
                    ->orWhere('ms.type', 'like', "%{$search}%")
                    ->orWhere('ms.qty', 'like', "%{$search}%")
                    ->orWhere('wh.warehouse', 'like', "%{$search}%");
            })
            ->orderByDesc('ms.msid')
            ->where('ms.company_id', $companyId)
            ->paginate($limit);
    }
    //---------------
    public function findMaterialsStockById(int $id, int $companyId)
    {
        return DB::table("{$this->table} as ms")
            ->leftJoin('materials as mat', 'ms.material_id', '=', 'mat.mid')
            ->leftJoin('warehouses as wh', 'ms.warehouse_id', '=', 'wh.wid')
            ->select([
                'ms.msid',
                'ms.material_id',
                'mat.material',
                'mat.unit',
                'ms.type',
                'ms.qty',
                'ms.date',
                'ms.warehouse_id',
                'wh.warehouse',
                'ms.company_id',
            ])
            ->where('ms.msid', $id)
            ->where('ms.company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkMaterialsStockExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('msid', $id)
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
            ->where('msid', $id)
            ->where('company_id', $companyId)
            ->update($data);
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('msid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
    //---------------
    public function checkMaterialBelongsToCompany(int $materialId, int $companyId): bool
    {
        return DB::table('materials')
            ->where('mid', $materialId)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function checkWarehouseBelongsToCompany(int $warehouseId, int $companyId): bool
    {
        return DB::table('warehouses')
            ->where('wid', $warehouseId)
            ->where('company_id', $companyId)
            ->exists();
    }
}
