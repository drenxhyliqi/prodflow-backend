<?php

namespace App\Repositories;

use App\Models\ExpensesModel;
use Illuminate\Support\Facades\DB;

class ExpensesRepository
{
    protected string $table;
    public function __construct(ExpensesModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getAllExpenses($limit)
    {
        return DB::table($this->table)
            ->orderByDesc('eid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedExpenses($search, $limit)
    {
        return DB::table($this->table)
            ->where('comment', 'like', "%{$search}%")
            ->orderByDesc('eid')
            ->paginate($limit);
    }
    //---------------
    public function findExpense(int $id)
    {
        return DB::table($this->table)
            ->where('eid', $id)
            ->first();
    }
    //---------------
    public function checkExpensesExist(int $id): bool
    {
        return DB::table($this->table)
            ->where('eid', $id)
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
            ->where('eid', $id)
            ->update($data) > 0;
    }
    //---------------
    public function delete(int $id): bool
    {
        return DB::table($this->table)
            ->where('eid', $id)
            ->delete() > 0;
    }
}
