<?php

namespace App\Repositories;

use App\Models\SalariesModel;
use Illuminate\Support\Facades\DB;

class SalariesRepository
{
    protected string $table;

    public function __construct(SalariesModel $model)
    {
        $this->table = $model->getTable();
    }

    public function getSalaries(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->join('staff', 'salaries.employee_id', '=', 'staff.sid')
            ->select(
                'salaries.*',
                'staff.name as employee_name',
                'staff.surname as employee_surname'
            )
            ->where('salaries.company_id', $companyId)
            ->orderByDesc('salaries.sid')
            ->paginate($limit);
    }

    public function getAllSalaries(int $companyId)
    {
        return DB::table($this->table)
            ->join('staff', 'salaries.employee_id', '=', 'staff.sid')
            ->select(
                'salaries.*',
                'staff.name as employee_name',
                'staff.surname as employee_surname'
            )
            ->where('salaries.company_id', $companyId)
            ->orderByDesc('salaries.sid')
            ->get();
    }

    public function getSearchedSalaries(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->join('staff', 'salaries.employee_id', '=', 'staff.sid')
            ->select(
                'salaries.*',
                'staff.name as employee_name',
                'staff.surname as employee_surname'
            )
            ->where('salaries.company_id', $companyId)
            ->where(function ($q) use ($search) {
                $q->where('staff.name', 'like', "%{$search}%")
                  ->orWhere('staff.surname', 'like', "%{$search}%")
                  ->orWhere('salaries.comment', 'like', "%{$search}%");
            })
            ->orderByDesc('salaries.sid')
            ->paginate($limit);
    }

    public function findSalary(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->first();
    }

    public function checkSalaryExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
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
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }

    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}