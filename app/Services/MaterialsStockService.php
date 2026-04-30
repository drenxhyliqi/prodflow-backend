<?php

namespace App\Services;

use App\Repositories\MaterialsStockRepository;

class MaterialsStockService
{
    protected MaterialsStockRepository $repository;
    public function __construct(MaterialsStockRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllMaterialsStock(int $limit, int $companyId, string $search = '')
    {
        if (isset($_GET['search']) && ! empty($_GET['search'])) {
            return $this->repository->getSearchedMaterialsStock($_GET['search'], $limit, $companyId);
        } else {
            return $this->repository->getAllMaterialsStock($limit, $companyId);
        }
    }
    //---------------
    public function getMaterialsStockById(int $id, int $companyId)
    {
        return $this->repository->findMaterialsStockById($id, $companyId);
    }
    //---------------   
    public function checkMaterialsStockExist(int $id, int $companyId): bool
    {
        return $this->repository->checkMaterialsStockExist($id, $companyId);
    }
    //---------------
    public function createMaterialsStock(array $data, int $companyId): bool
    {
        return $this->repository->create($data, $companyId);
    }
    //---------------
    public function updateMaterialsStock(int $id, array $data, int $companyId): bool
    {
        $record = $this->repository->findMaterialsStockById($id, $companyId);

        if (! $record) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteMaterialsStock(int $id, int $companyId): bool
    {
        $record = $this->repository->findMaterialsStockById($id, $companyId);

        if (! $record) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function checkMaterialBelongsToCompany(int $materialId, int $companyId): bool
    {
        return $this->repository->checkMaterialBelongsToCompany($materialId, $companyId);
    }
    //---------------
    public function checkWarehouseBelongsToCompany(int $warehouseId, int $companyId): bool
    {
        return $this->repository->checkWarehouseBelongsToCompany($warehouseId, $companyId);
    }
}
