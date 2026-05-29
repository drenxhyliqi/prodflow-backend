<?php

namespace App\Services;

use App\Repositories\VacationsRepository;

class VacationsService
{
    protected VacationsRepository $repository;

    public function __construct(VacationsRepository $repository)
    {
        $this->repository = $repository;
    }

    //---------------
    public function getVacations(int $companyId, int $limit, ?string $search)
    {
        if ($search) {
            return $this->repository->getSearchedVacations($companyId, $limit, $search);
        }
        return $this->repository->getAllVacations($companyId, $limit);
    }

    //---------------
    public function findVacation(int $id, int $companyId)
    {
        return $this->repository->findVacation($id, $companyId);
    }

    //---------------
    public function create(array $data, int $companyId): bool
    {
        $created = $this->repository->create($data, $companyId);
        return $created;
    }

    //---------------
    public function update(int $id, array $data, int $companyId): bool
    {
        if (!$this->repository->checkVacationExist($id, $companyId)) {
            return false;
        }
        $updated = $this->repository->update($id, $data, $companyId);
        return $updated;
    }

    //---------------
    public function delete(int $id, int $companyId): bool
    {
        if (!$this->repository->checkVacationExist($id, $companyId)) {
            return false;
        }
        $deleted = $this->repository->delete($id, $companyId);
        return $deleted;
    }
}
