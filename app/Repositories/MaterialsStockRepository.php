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

    public function getAllMaterialsStock(int $limit, ?int $companyId = null)
    {
        $query = DB::table("{$this->table} as ms")
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
            ->orderByDesc('ms.msid');

        if ($companyId !== null) {
            $query->where('ms.company_id', $companyId);
        }

        return $query->paginate($limit);
    }

    public function getSearchedMaterialsStock(string $search, int $limit, ?int $companyId = null)
    {
        $query = DB::table("{$this->table} as ms")
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
            ->orderByDesc('ms.msid');

        if ($companyId !== null) {
            $query->where('ms.company_id', $companyId);
        }

        return $query->paginate($limit);
    }

    public function findMaterialsStockById(int $id, ?int $companyId = null)
    {
        $query = DB::table("{$this->table} as ms")
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
            ->where('ms.msid', $id);

        if ($companyId !== null) {
            $query->where('ms.company_id', $companyId);
        }

        return $query->first();
    }

    public function checkMaterialsStockExist(int $id, ?int $companyId = null): bool
    {
        $query = DB::table($this->table)->where('msid', $id);

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
        $query = DB::table($this->table)->where('msid', $id);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->update($data) > 0;
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        $query = DB::table($this->table)->where('msid', $id);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return (bool) $query->delete();
    }

    public function checkMaterialBelongsToCompany(int $materialId, int $companyId): bool
    {
        return DB::table('materials')
            ->where('mid', $materialId)
            ->where('company_id', $companyId)
            ->exists();
    }

    public function checkWarehouseBelongsToCompany(int $warehouseId, int $companyId): bool
    {
        return DB::table('warehouses')
            ->where('wid', $warehouseId)
            ->where('company_id', $companyId)
            ->exists();
    }
}
