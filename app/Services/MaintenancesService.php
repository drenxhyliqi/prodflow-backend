<?php

namespace App\Services;

use App\Repositories\MaintenancesRepository;

class MaintenancesService
{
    protected MaintenancesRepository $repository;

    public function __construct(MaintenancesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllMaintenances(int $companyId, int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedMaintenances($companyId, $limit, $search);
        }
        return $this->repository->getAllMaintenances($companyId, $limit);
    }

    public function getById(int $id, int $companyId)
    {
        return $this->repository->findMaintenance($id, $companyId);
    }

    public function findOrFail(int $id, int $companyId)
    {
        return $this->repository->checkMaintenanceExist($id, $companyId);
    }

    public function createMaintenance(array $data, int $companyId)
    {
        return $this->repository->create($data, $companyId);
    }

    public function updateMaintenance(int $id, array $data, int $companyId): bool
    {
        if (!$this->repository->checkMaintenanceExist($id, $companyId)) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }

    public function deleteMaintenance(int $id, int $companyId): bool
    {
        if (!$this->repository->checkMaintenanceExist($id, $companyId)) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}