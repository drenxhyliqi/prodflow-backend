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
        $created = $this->repository->create($data, $companyId);
        if ($created) {
            AnalyticsCacheService::dispatchRefresh($companyId);
        }
        return $created;
    }
    //---------------
    public function updateMaterialsStock(int $id, array $data, int $companyId): bool
    {
        $record = $this->repository->findMaterialsStockById($id, $companyId);

        if (! $record) {
            return false;
        }

        $updated = $this->repository->update($id, $data, $companyId);
        if ($updated) {
            AnalyticsCacheService::dispatchRefresh($companyId);
        }

        return $updated;
    }
    //---------------
    public function deleteMaterialsStock(int $id, int $companyId): bool
    {
        $record = $this->repository->findMaterialsStockById($id, $companyId);

        if (! $record) {
            return false;
        }

        $deleted = $this->repository->delete($id, $companyId);
        if ($deleted) {
            AnalyticsCacheService::dispatchRefresh($companyId);
        }

        return $deleted;
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
    // Returns remaining capacity for a specific warehouse (null = no capacity configured, skip check)
    public function getRemainingCapacity(int $companyId, ?int $excludeMsid = null, ?int $warehouseId = null): ?float
    {
        if ($warehouseId !== null) {
            $capacity = (float) \App\Models\WarehousesModel::where('wid', $warehouseId)
                ->where('company_id', $companyId)
                ->value('capacity');

            if ($capacity <= 0) {
                return null;
            }

            $query = \App\Models\MaterialsStockModel::where('warehouse_id', $warehouseId);

            if ($excludeMsid !== null) {
                $query->where('msid', '!=', $excludeMsid);
            }

            $netStock = (float) $query
                ->selectRaw('SUM(CASE WHEN type = "IN" THEN qty ELSE -qty END) as net')
                ->value('net');

            return max(0, $capacity - $netStock);
        }

        // Fallback: global check (legacy)
        $totalCapacity = \App\Models\WarehousesModel::where('company_id', $companyId)->sum('capacity');

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
    //---------------
    public function getStockReportData(int $companyId, string $startDate, string $endDate)
    {
        return \App\Models\MaterialsStockModel::with(['material'])
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'msid' => $item->msid,
                    'date' => $item->date,
                    'material_name' => $item->material ? $item->material->material : 'Unknown Material',
                    'type' => $item->type,
                    'qty' => (float) $item->qty,
                    'warehouse_id' => $item->warehouse_id,
                ];
            });
    }
}
