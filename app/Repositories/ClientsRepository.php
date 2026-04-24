<?php

namespace App\Repositories;

use App\Models\ClientsModel;
use Illuminate\Support\Facades\DB;

class ClientsRepository
{
    protected string $table;
    public function __construct(ClientsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllClients($limit)
    {
        return DB::table($this->table)
            ->orderByDesc('cid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedClients($search, $limit)
    {
        return DB::table($this->table)
            ->where('client', 'like', "%{$search}%")
            ->orderByDesc('cid')
            ->paginate($limit);
    }
    //---------------
    public function findClient(int $id)
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->first();
    }
    //---------------
    public function checkClientExist(int $id): bool
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
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->delete() > 0;
    }
}
