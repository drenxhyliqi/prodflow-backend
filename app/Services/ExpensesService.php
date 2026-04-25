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
    public function getAllExpenses($limit)
    {
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            return $this->repository->getSearchedExpenses($_GET['search'], $limit);
        } else {
            return $this->repository->getAllExpenses($limit);
        }
    }
    //---------------
    public function getExpenseById(int $id)
    {
        return $this->repository->findExpense($id);
    }
    //---------------
    public function findOrFail(int $id)
    {
        return $this->repository->checkExpensesExist($id);
    }
    //---------------
    public function createExpense(array $data)
    {
        return $this->repository->create($data);
    }
    //---------------
    public function updateExpense(int $id, array $data): bool
    {
        $expense = $this->repository->findExpense($id);
        if (!$expense) {
            return false;
        }
        return $this->repository->update($id, $data);
    }
    //---------------
    public function deleteExpense(int $id): bool
    {
        return $this->repository->delete($id);
    }
    //---------------
}
