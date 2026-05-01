<?php

namespace App\Repositories;

use App\Models\UsersModel;
use Illuminate\Support\Facades\DB;

class UsersRepository
{
    protected string $table;
    public function __construct(UsersModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getUsers(int $limit)
    {
        return DB::table($this->table)
            ->join('companies', 'companies.cid', '=', 'users.company_id')
            ->select(
                'users.*',
                'companies.name as company'
            )
            ->where('role', '!=', 'superadmin')
            ->orderByDesc('uid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedUsers(string $search, int $limit)
    {
        return DB::table($this->table)
            ->where('user', 'like', "%{$search}%")
            ->where('role', '!=', 'superadmin')
            ->orderByDesc('uid')
            ->paginate($limit);
    }
    //---------------
    public function findUser(int $id)
    {
        return DB::table($this->table)
            ->where('uid', $id)
            ->where('role', '!=', 'superadmin')
            ->first();
    }
    //---------------
    public function checkUserExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('uid', $id)
            ->where('role', '!=', 'superadmin')
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
            ->where('uid', $id)
            ->where('role', '!=', 'superadmin')
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('uid', $id)
            ->where('role', '!=', 'superadmin')
            ->delete() > 0;
    }
    //---------------
    public function findByUsername(string $username)
    {
        return UsersModel::where('username', $username)->first();
    }
    //---------------
    public function updateAccount(string $username, array $data): bool
    {
        return DB::table($this->table)
            ->where('username', $username)
            ->where('role', '!=', 'superadmin')
            ->update($data) > 0;
    }
}
