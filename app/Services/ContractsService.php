<?php

namespace App\Services;

use App\Repositories\ContractsRepository;

class ContractsService
{
    protected ContractsRepository $repository;
    public function __construct(ContractsRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getContracts(int $limit = 10, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedContracts($search, $limit);
        }
        return $this->repository->getContracts($limit);
    }
    //---------------
    public function getAllContracts()
    {
        return $this->repository->getAllContracts();
    }
    //---------------
    public function getContractById(int $id)
    {
        return $this->repository->findContract($id);
    }
    //---------------
    public function findOrFail(int $id)
    {
        return $this->repository->checkContractExist($id);
    }
    //---------------
    public function createContract(array $data)
    {
        $created = $this->repository->create($data);
        if ($created && !empty($data['company_id'])) {
            AnalyticsCacheService::dispatchRefresh((int) $data['company_id']);
        }
        return $created;
    }
    //---------------
    public function updateContract(int $id, array $data): bool
    {
        $contract = $this->repository->findContract($id);
        if (!$contract) {
            return false;
        }
        $updated = $this->repository->update($id, $data);
        if ($updated) {
            AnalyticsCacheService::dispatchRefresh((int) $contract->company_id);
        }
        return $updated;
    }
    //---------------
    public function deleteContract(int $id): bool
    {
        $contract = $this->repository->findContract($id);
        if (!$contract) {
            return false;
        }
        $deleted = $this->repository->delete($id);
        if ($deleted) {
            AnalyticsCacheService::dispatchRefresh((int) $contract->company_id);
        }
        return $deleted;
    }
    //---------------
    public function activateContract(int $id): bool
    {
        $contract = $this->repository->findContract($id);
        if (!$contract) {
            return false;
        }
        $updated = $this->repository->changeStatus($id, 'Active');
        if ($updated) {
            AnalyticsCacheService::dispatchRefresh((int) $contract->company_id);
        }
        return $updated;
    }
    //---------------
    public function deactivateContract(int $id): bool
    {
        $contract = $this->repository->findContract($id);
        if (!$contract) {
            return false;
        }
        $updated = $this->repository->changeStatus($id, 'Deactive');
        if ($updated) {
            AnalyticsCacheService::dispatchRefresh((int) $contract->company_id);
        }
        return $updated;
    }
}