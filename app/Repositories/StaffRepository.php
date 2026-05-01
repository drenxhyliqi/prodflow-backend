<?php

namespace App\Repositories;

use App\Models\StaffModel;
use Illuminate\Support\Facades\DB;

class StaffRepository
{
    protected string $table;

    public function __construct(StaffModel $model)
    {
        $this->table = $model->getTable();
    }

    public function getAllStaff(int $limit, int $companyId)
    {
        return DB::table($this->table)->orderByDesc('sid')
            ->where('company_id', $companyId)
            ->paginate($limit);

    }
    //---------------
    public function getSearchedStaff(string $search, int $limit, int $companyId)
    {
        return DB::table($this->table)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%");
            })
            ->where('company_id', $companyId)
            ->orderByDesc('sid')
            ->paginate($limit);

    }
    //---------------
    public function findStaffById(int $id, int $companyId)
    {
        return DB::table($this->table)->where('sid', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkStaffExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
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

    public function hasDuplicateStaff(
        string $name,
        string $surname,
        string $position,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($name))])
            ->whereRaw('LOWER(surname) = ?', [mb_strtolower(trim($surname))])
            ->whereRaw('LOWER(position) = ?', [mb_strtolower(trim($position))])
            ->where('sid', '!=', $excludeId)
            ->exists();
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
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}
