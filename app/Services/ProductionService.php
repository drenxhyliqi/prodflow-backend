<?php

namespace App\Services;

use App\Repositories\ProductionRepository;
use Illuminate\Support\Facades\Cache;

class ProductionService
{
    protected ProductionRepository $repository;
    public function __construct(ProductionRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllProduction(int $limit, int $companyId, string $search, int $page)
    {
        if (!empty($search)) {
            return $this->repository->getSearchedProduction($search, $limit, $companyId);
        }

        $cacheKey = "production_company_{$companyId}_page_{$page}";
        return Cache::tags(['production'])->remember(
            $cacheKey,
            now()->addHours(3),
            function () use ($limit, $companyId) {
                return $this->repository->getAllProduction($limit, $companyId);
            }
        );
    }
    //---------------
    public function getProductionById(int $id, int $companyId)
    {
        return $this->repository->findProductionById($id, $companyId);
    }
    //---------------
    public function checkProductionExist(int $id, int $companyId): bool
    {
        return $this->repository->checkProductionExist($id, $companyId);
    }
    //---------------
    public function createProduction(array $data, int $companyId): bool
    {
        Cache::tags(['production'])->flush();
        return $this->repository->create($data, $companyId);
    }
    //---------------
    public function hasDuplicateProduction(
        int $productId,
        int $machineId,
        float $qty,
        string $date,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return $this->repository->hasDuplicateProduction($productId, $machineId, $qty, $date, $companyId, $excludeId);
    }
    //---------------
    public function updateProduction(int $id, array $data, int $companyId): bool
    {
        $production = $this->repository->findProductionById($id, $companyId);

        if (! $production) {
            return false;
        }
        Cache::tags(['production'])->flush();
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteProduction(int $id, int $companyId): bool
    {
        $production = $this->repository->findProductionById($id, $companyId);

        if (! $production) {
            return false;
        }
        Cache::tags(['production'])->flush();
        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function checkProductBelongsToCompany(int $productId, int $companyId): bool
    {
        return $this->repository->checkProductBelongsToCompany($productId, $companyId);
    }
    //---------------
    public function checkMachineBelongsToCompany(int $machineId, int $companyId): bool
    {
        return $this->repository->checkMachineBelongsToCompany($machineId, $companyId);
    }
}
