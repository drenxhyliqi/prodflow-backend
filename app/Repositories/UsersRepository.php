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
    public function countAll(): int
    {
        return DB::table($this->table)->count();
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
    //---------------
    public function createInvitation(array $data): bool
    {
        return DB::table('user_invitations')->insert($data);
    }
    //---------------
    public function getInvitations(int $limit, string $search = '')
    {
        DB::table('user_invitations')
            ->leftJoin('companies', 'companies.cid', '=', 'user_invitations.company_id')
            ->select(
                'user_invitations.*',
                'companies.name as company'
            )
            ->orderByDesc('user_invitations.iid');

        if (!empty($search)) {
            DB::table('user_invitations')
                ->where(function ($q) use ($search) {
                    $q->where('user_invitations.user', 'like', "%{$search}%")
                        ->orWhere('user_invitations.username', 'like', "%{$search}%");
                });
        }

        return DB::table('user_invitations')->paginate($limit);
    }
    //---------------
    public function findValidInvitationByToken(string $token)
    {
        return DB::table('user_invitations')
            ->where('token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();
    }
    //---------------
    public function updateInvitationStatus(int $id, string $status): bool
    {
        return DB::table('user_invitations')
            ->where('iid', $id)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]) > 0;
    }
}
