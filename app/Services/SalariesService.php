<?php

namespace App\Services;

use App\Repositories\SalariesRepository;

class SalariesService
{
    protected SalariesRepository $repository;
    public function __construct(SalariesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getSalaries(int $companyId, int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedSalaries($companyId, $limit, $search);
        }
        return $this->repository->getSalaries($companyId, $limit);
    }

    public function getAllSalaries(int $companyId)
    {
        return $this->repository->getAllSalaries($companyId);
    }

    public function getSalaryById(int $id, int $companyId)
    {
        return $this->repository->findSalary($id, $companyId);
    }

    public function findOrFail(int $id, int $companyId)
    {
        return $this->repository->checkSalaryExist($id, $companyId);
    }

    public function createSalary(array $data, int $companyId)
    {
        return $this->repository->create($data, $companyId);
    }

    public function updateSalary(int $id, array $data, int $companyId): bool
    {
        $salary = $this->repository->findSalary($id, $companyId);
        if (!$salary) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }

    public function deleteSalary(int $id, int $companyId): bool
    {
        $salary = $this->repository->findSalary($id, $companyId);
        if (!$salary) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}