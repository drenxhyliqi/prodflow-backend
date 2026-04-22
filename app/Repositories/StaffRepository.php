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

    public function getAllStaff(int $limit, ?int $companyId = null)
    {
        $query = DB::table($this->table)->orderByDesc('sid');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->paginate($limit);
    }

    public function getSearchedStaff(string $search, int $limit, ?int $companyId = null)
    {
        $query = DB::table($this->table)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%");
            })
            ->orderByDesc('sid');

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->paginate($limit);
    }

    public function findStaffById(int $id, ?int $companyId = null)
    {
        $query = DB::table($this->table)->where('sid', $id);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->first();
    }

    public function checkStaffExist(int $id, ?int $companyId = null): bool
    {
        $query = DB::table($this->table)->where('sid', $id);

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
        $query = DB::table($this->table)->where('sid', $id);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return $query->update($data) > 0;
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        $query = DB::table($this->table)->where('sid', $id);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        return (bool) $query->delete();
    }
}
