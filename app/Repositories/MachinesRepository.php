<?php

namespace App\Repositories;

use App\Models\MachinesModel;
use Illuminate\Support\Facades\DB;

class MachinesRepository
{
    protected string $table;

    public function __construct(MachinesModel $model)
    {
        $this->table = $model->getTable();
    }

    public function getAllMachines($limit)
    {
        return DB::table($this->table)
            ->orderByDesc('mid')
            ->paginate($limit);
    }

    public function getSearchedMachines($search, $limit)
    {
        return DB::table($this->table)
            ->where('machine', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%")
            ->orderByDesc('mid')
            ->paginate($limit);
    }

    public function findMachine(int $id)
    {
        return DB::table($this->table)
            ->where('mid', $id)
            ->first();
    }

    public function checkMachineExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('mid', $id)
            ->exists();
    }

    public function create(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
    }

    public function update(int $id, array $data): bool
    {
        return DB::table($this->table)
            ->where('mid', $id)
            ->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('mid', $id)
            ->delete() > 0;
    }
}