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
    public function getAllClients($limit)
    {
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            return $this->repository->getSearchedClients($_GET['search'], $limit);
        } else {
            return $this->repository->getAllClients($limit);
        }
    }
    //---------------
    public function getClientById(int $id)
    {
        return $this->repository->findClient($id);
    }
    //---------------
    public function findOrFail(int $id)
    {
        return $this->repository->checkClientExist($id);
    }
    //---------------
    public function createClient(array $data)
    {
        return $this->repository->create($data);
    }
    //---------------
    public function updateClient(int $id, array $data): bool
    {
        $client = $this->repository->findClient($id);
        if (!$client) {
            return false;
        }
        return $this->repository->update($id, $data);
    }
    //---------------
    public function deleteClient(int $id): bool
    {
        return $this->repository->delete($id);
    }
    //---------------
}
