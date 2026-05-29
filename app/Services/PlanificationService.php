<?php

namespace App\Services;

use App\Repositories\PlanificationRepository;

class PlanificationService
{
    protected PlanificationRepository $repository;

    public function __construct(PlanificationRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllPlanification(int $companyId, int $limit)
    {
        $limit = (int) $limit ?: 10;
        return $this->repository->getAllPlanification($companyId, $limit);
    }
    //---------------
    public function getPlanificationById(int $id, int $companyId)
    {
        return $this->repository->findPlanification($id, $companyId);
    }
    //---------------
    public function checkPlanificationExist(int $id, int $companyId): bool
    {
        return $this->repository->checkPlanificationExist($id, $companyId);
    }
    //---------------
    public function createPlanification(array $data, int $companyId): bool
    {
        $created = $this->repository->create($data, $companyId);
        if ($created) {
            AnalyticsCacheService::dispatchRefresh($companyId);
        }
        return $created;
    }
    //---------------
    public function updatePlanification(int $id, array $data, int $companyId): bool
    {
        $plan = $this->repository->findPlanification($id, $companyId);
        if (!$plan) {
            return false;
        }
        $updated = $this->repository->update($id, $data, $companyId);
        if ($updated) {
            AnalyticsCacheService::dispatchRefresh($companyId);
        }
        return $updated;
    }
    //---------------
    public function deletePlanification(int $id, int $companyId): bool
    {
        $plan = $this->repository->findPlanification($id, $companyId);
        if (!$plan) {
            return false;
        }
        $deleted = $this->repository->delete($id, $companyId);
        if ($deleted) {
            AnalyticsCacheService::dispatchRefresh($companyId);
        }
        return $deleted;
    }
}
