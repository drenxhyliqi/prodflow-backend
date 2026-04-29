<?php

namespace App\Services;

use App\Repositories\ExpensesRepository;

class ExpensesService
{
    protected ExpensesRepository $repository;
    public function __construct(ExpensesRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllExpenses(int $companyId, int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedExpenses($companyId, $limit, $search);
        }

        return $this->repository->getAllExpenses($companyId, $limit);
    }
    //---------------
    public function getExpenseById(int $id, int $companyId)
    {
        return $this->repository->findExpense($id, $companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId)
    {
        return $this->repository->checkExpenseExist($id, $companyId);
    }
    //---------------
    public function createExpense(array $data, int $companyId)
    {
        return $this->repository->create($data, $companyId);
    }
    //---------------
    public function updateExpense(int $id, array $data, int $companyId): bool
    {
        $expense = $this->repository->findExpense($id, $companyId);
        if (!$expense) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteExpense(int $id, int $companyId): bool
    {
        $expense = $this->repository->findExpense($id, $companyId);
        if (!$expense) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}
