<?php

namespace App\Services;

use App\Repositories\WarehousesRepository;

class WarehousesService
{
    protected WarehousesRepository $repository;

    public function __construct(WarehousesRepository $repository)
    {
        $this->repository = $repository;
    }

    //---------------
    public function getAllWarehouses(int $companyId, int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedWarehouses($companyId, $limit, $search);
        }

        return $this->repository->getAllWarehouses($companyId, $limit);
    }

    //---------------
    public function getWarehouseById(int $id, int $companyId)
    {
        return $this->repository->findWarehouse($id, $companyId);
    }

    //---------------
    public function findOrFail(int $id, int $companyId)
    {
        return $this->repository->checkWarehouseExist($id, $companyId);
    }

    //---------------
    public function createWarehouse(array $data, int $companyId)
    {
        return $this->repository->create($data, $companyId);
    }

    //---------------
    public function updateWarehouse(int $id, array $data, int $companyId): bool
    {
        $warehouse = $this->repository->findWarehouse($id, $companyId);
        if (!$warehouse) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }

    //---------------
    public function deleteWarehouse(int $id, int $companyId): bool
    {
        $warehouse = $this->repository->findWarehouse($id, $companyId);
        if (!$warehouse) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}