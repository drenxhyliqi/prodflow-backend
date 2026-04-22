<?php

namespace App\Repositories;

use App\Models\CompaniesModel;
use Illuminate\Support\Facades\DB;

class CompanyRepository
{
    protected string $table;
    public function __construct(CompaniesModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllCompanies($limit)
    {
        return DB::table($this->table)
            ->orderByDesc('id')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedCompanies($search, $limit)
    {
        return DB::table($this->table)
            ->where('name', 'like', "%{$search}%")
            ->orderByDesc('id')
            ->paginate($limit);
    }
    //---------------
    public function findCompany(int $id)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->first();
    }
    //---------------
    public function checkCompanyExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->exists();
    }
    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
    }
    //---------------
    public function update(int $id, array $data): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update($data) > 0;
    }
    //---------------
    public function changeStatus(int $id, $status): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'status' => $status
            ]) > 0;
    }
    //---------------
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->delete() > 0;
    }
}
