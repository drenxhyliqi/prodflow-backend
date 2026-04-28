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
    public function getCompanies($limit)
    {
        return DB::table($this->table)
            ->orderByDesc('cid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedCompanies(string $search, int $limit)
    {
        return DB::table($this->table)
            ->where('name', 'like', "%{$search}%")
            ->orderByDesc('cid')
            ->paginate($limit);
    }
    //---------------
    public function getAllCompanies()
    {
        return DB::table($this->table)
            ->orderByDesc('cid')
            ->get();
    }
    //---------------
    public function findCompany(int $id)
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->first();
    }
    //---------------
    public function checkCompanyExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
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
            ->where('cid', $id)
            ->update($data) > 0;
    }
    //---------------
    public function changeStatus(int $id, string $status): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->update([
                'status' => $status
            ]) > 0;
    }
    //---------------
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->delete() > 0;
    }
}
