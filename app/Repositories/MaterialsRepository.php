<?php

namespace App\Repositories;

use App\Models\MaterialsModel;
use Illuminate\Support\Facades\DB;

class MaterialsRepository
{
    protected string $table;

    public function __construct(MaterialsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllMaterials(int $limit, int $companyId, string $search = '')
    {
        return DB::table($this->table)
            ->where(function ($q) use ($search) {
                $q->where('material', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%");
            })
            ->orderByDesc('mid')
            ->where('company_id', $companyId)
            ->paginate($limit);
    }       
    //---------------
    public function findMaterialById(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('mid', $id)
            ->where('company_id', $companyId)
            ->first();

    }
    //---------------
    public function checkMaterialExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('mid', $id)
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
    public function hasDuplicateMaterial(
        string $material,
        string $unit,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->whereRaw('LOWER(material) = ?', [mb_strtolower(trim($material))])
            ->whereRaw('LOWER(unit) = ?', [mb_strtolower(trim($unit))])
            ->where('mid', '!=', $excludeId)
            ->exists();
    }
    //---------------
    public function update(int $id, array $data, int $companyId): bool
    {
        return DB::table($this->table)->where('mid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
        ->where('mid', $id)
        ->where('company_id', $companyId)
        ->delete() > 0;
    }
}
