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
    public function getAllClients(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->orderByDesc('cid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedClients(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('client', 'like', "%{$search}%")
            ->orderByDesc('cid')
            ->paginate($limit);
    }
    //---------------
    public function findClient(int $id, int $companyId)
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->where('company_id', $companyId)
            ->first();
    }
    //---------------
    public function checkClientExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
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
            ->where('cid', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('cid', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
}
