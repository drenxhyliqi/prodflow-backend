<?php

namespace App\Services;

use App\Repositories\ProductionRepository;

class ProductionService
{
    public function __construct(
        protected ProductionRepository $repository
    ) {}

    public function getAllProduction(int $limit, ?int $companyId = null)
    {
        if (isset($_GET['search']) && ! empty($_GET['search'])) {
            return $this->repository->getSearchedProduction($_GET['search'], $limit, $companyId);
        } else {
            return $this->repository->getAllProduction($limit, $companyId);
        }
    }

    public function getProductionById(int $id, ?int $companyId = null)
    {
        return $this->repository->findProductionById($id, $companyId);
    }

    public function checkProductionExist(int $id, ?int $companyId = null): bool
    {
        return $this->repository->checkProductionExist($id, $companyId);
    }

    public function createProduction(array $data): bool
    {
        return $this->repository->create($data);
    }

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

    public function updateProduction(int $id, array $data, ?int $companyId = null): bool
    {
        $production = $this->repository->findProductionById($id, $companyId);

        if (! $production) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }

    public function deleteProduction(int $id, ?int $companyId = null): bool
    {
        $production = $this->repository->findProductionById($id, $companyId);

        if (! $production) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }

    public function checkProductBelongsToCompany(int $productId, int $companyId): bool
    {
        return $this->repository->checkProductBelongsToCompany($productId, $companyId);
    }

    public function checkMachineBelongsToCompany(int $machineId, int $companyId): bool
    {
        return $this->repository->checkMachineBelongsToCompany($machineId, $companyId);
    }
}
