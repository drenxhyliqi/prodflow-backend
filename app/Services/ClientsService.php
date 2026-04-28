<?php

namespace App\Services;

use App\Repositories\ClientsRepository;

class ClientsService
{
    protected ClientsRepository $repository;
    public function __construct(ClientsRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllClients(int $companyId, int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedClients($companyId, $limit, $search);
        }

        return $this->repository->getAllClients($companyId, $limit);
    }
    //---------------
    public function getClientById(int $id, int $companyId)
    {
        return $this->repository->findClient($id, $companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId)
    {
        return $this->repository->checkClientExist($id, $companyId);
    }
    //---------------
    public function createClient(array $data, int $companyId)
    {
        return $this->repository->create($data, $companyId);
    }
    //---------------
    public function updateClient(int $id, array $data, int $companyId): bool
    {
        $client = $this->repository->findClient($id, $companyId);
        if (!$client) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteClient(int $id, int $companyId): bool
    {
        $company = $this->repository->findClient($id, $companyId);
        if (!$company) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}
