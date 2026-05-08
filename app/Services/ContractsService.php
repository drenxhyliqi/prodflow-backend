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
        return $this->repository->create($data);
    }
    //---------------
    public function updateContract(int $id, array $data): bool
    {
        $contract = $this->repository->findContract($id);
        if (!$contract) {
            return false;
        }
        return $this->repository->update($id, $data);
    }
    //---------------
    public function deleteContract(int $id): bool
    {
        $contract = $this->repository->findContract($id);
        if (!$contract) {
            return false;
        }
        return $this->repository->delete($id);
    }
    //---------------
    public function activateContract(int $id): bool
    {
        return $this->repository->changeStatus($id, 'Active');
    }
    //---------------
    public function deactivateContract(int $id): bool
    {
        return $this->repository->changeStatus($id, 'Deactive');
    }
}