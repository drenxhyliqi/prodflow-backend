<?php

namespace App\Repositories;

use App\Models\VacationsModel;
use Illuminate\Support\Facades\DB;

class VacationsRepository
{
    protected string $table;

    public function __construct(VacationsModel $model)
    {
        $this->table = $model->getTable();
    }

    //---------------
    public function getAllVacations(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->join('staff', 'vacations.staff_id', '=', 'staff.sid')
            ->select('vacations.*', DB::raw("CONCAT(staff.name, ' ', staff.surname) as staff_name"))
            ->where('vacations.company_id', $companyId)
            ->orderByDesc('vacations.vid')
            ->paginate($limit);
    }

    //---------------
    public function getSearchedVacations(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->join('staff', 'vacations.staff_id', '=', 'staff.sid')
            ->select('vacations.*', DB::raw("CONCAT(staff.name, ' ', staff.surname) as staff_name"))
            ->where('vacations.company_id', $companyId)
            ->where(function ($query) use ($search) {
                $query->where('staff.name', 'like', "%{$search}%")
                    ->orWhere('staff.surname', 'like', "%{$search}%")
                    ->orWhere('vacations.reason', 'like', "%{$search}%")
                    ->orWhere('vacations.status', 'like', "%{$search}%");
            })
            ->orderByDesc('vacations.vid')
            ->paginate($limit);
    }

    //---------------
    public function findVacation(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->join('staff', 'vacations.staff_id', '=', 'staff.sid')
            ->select('vacations.*', DB::raw("CONCAT(staff.name, ' ', staff.surname) as staff_name"))
            ->where('vacations.vid', $id)
            ->where('vacations.company_id', $companyId)
            ->first();
    }

    //---------------
    public function checkVacationExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('vid', $id)
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
            ->where('vid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }

    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('vid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}
