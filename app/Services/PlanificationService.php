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
        return $this->repository->create($data, $companyId);
    }
    //---------------
    public function updatePlanification(int $id, array $data, int $companyId): bool
    {
        $plan = $this->repository->findPlanification($id, $companyId);
        if (!$plan) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deletePlanification(int $id, int $companyId): bool
    {
        $plan = $this->repository->findPlanification($id, $companyId);
        if (!$plan) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}
