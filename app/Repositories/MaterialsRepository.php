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

    public function getAllMaterials(int $limit, ?int $companyId = null)
    {
        $query = DB::table($this->table)->orderByDesc('mid');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->paginate($limit);
    }

    public function getSearchedMaterials(string $search, int $limit, ?int $companyId = null)
    {
        $query = DB::table($this->table)
            ->where(function ($q) use ($search) {
                $q->where('material', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%");
            })
            ->orderByDesc('mid');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->paginate($limit);
    }

    public function findMaterialById(int $id, ?int $companyId = null)
    {
        return DB::table($this->table)
            ->where('mid', $id)
            ->where('company_id', $companyId)
            ->first();

    }

    public function checkMaterialExist(int $id, ?int $companyId = null): bool
    {
        return DB::table($this->table)
            ->where('mid', $id)
            ->where('company_id', $companyId)
            ->exists();

    }

    public function create(array $data): bool
    {
        return DB::table($this->table)->insert($data);
    }

    public function hasDuplicateMaterial(
        string $material,
        string $unit,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        $query = DB::table($this->table)
            ->where('company_id', $companyId)
            ->whereRaw('LOWER(material) = ?', [mb_strtolower(trim($material))])
            ->whereRaw('LOWER(unit) = ?', [mb_strtolower(trim($unit))]);

        if ($excludeId !== null) {
            $query->where('mid', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function update(int $id, array $data, ?int $companyId = null): bool
    {
        return DB::table($this->table)->where('mid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        return DB::table($this->table)
        ->where('mid', $id)
        ->where('company_id', $companyId)
        ->delete() > 0;
    }
}
