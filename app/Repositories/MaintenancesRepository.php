<?php

namespace App\Repositories;

use App\Models\MaintenancesModel;
use Illuminate\Support\Facades\DB;

class MaintenancesRepository
{
    protected string $table;

    public function __construct(MaintenancesModel $model)
    {
        $this->table = $model->getTable();
    }

    public function getAllMaintenances(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->join('machines', 'maintenances.machine_id', '=', 'machines.mid')
            ->select('maintenances.*', 'machines.machine as machine_name')
            ->where('maintenances.company_id', $companyId)
            ->orderByDesc('maintenances.mid')
            ->paginate($limit);
    }

    public function getSearchedMaintenances(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->join('machines', 'maintenances.machine_id', '=', 'machines.mid')
            ->select('maintenances.*', 'machines.machine as machine_name')
            ->where('maintenances.company_id', $companyId)
            ->where(function($query) use ($search) {
                $query->where('maintenances.description', 'like', "%{$search}%")
                      ->orWhere('machines.machine', 'like', "%{$search}%");
            })
            ->orderByDesc('maintenances.mid')
            ->paginate($limit);
    }

    public function findMaintenance(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('mid', $id)
            ->where('company_id', $companyId)
            ->first();
    }

    public function checkMaintenanceExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('mid', $id)
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
            ->where('mid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }

    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('mid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}