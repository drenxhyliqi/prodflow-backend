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
    //---------------
    // Returns current net stock for a specific material (IN - OUT)
    public function getCurrentStock(int $companyId, int $materialId, ?int $excludeMsid = null): float
    {
        $query = \App\Models\MaterialsStockModel::where('company_id', $companyId)
            ->where('material_id', $materialId);

        if ($excludeMsid !== null) {
            $query->where('msid', '!=', $excludeMsid);
        }

        return max(0, (float) $query
            ->selectRaw('SUM(CASE WHEN type = "IN" THEN qty ELSE -qty END) as net')
            ->value('net'));
    }
    //---------------
    // Returns remaining capacity (null = no capacity configured, skip check)
    public function getRemainingCapacity(int $companyId, ?int $excludeMsid = null): ?float
    {
        $totalCapacity = \App\Models\WarehousesModel::where('company_id', $companyId)
            ->sum('capacity');

        if (!$totalCapacity || (float) $totalCapacity <= 0) {
            return null;
        }

        $query = \App\Models\MaterialsStockModel::where('company_id', $companyId);

        if ($excludeMsid !== null) {
            $query->where('msid', '!=', $excludeMsid);
        }

        $netStock = (float) $query
            ->selectRaw('SUM(CASE WHEN type = "IN" THEN qty ELSE -qty END) as net')
            ->value('net');

        return max(0, (float) $totalCapacity - $netStock);
    }
}
