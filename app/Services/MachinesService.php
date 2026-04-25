<?php

namespace App\Services;

use App\Repositories\MachinesRepository;

class MachinesService
{
    protected MachinesRepository $repository;

    public function __construct(MachinesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllMachines($limit)
    {
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            return $this->repository->getSearchedMachines($_GET['search'], $limit);
        } else {
            return $this->repository->getAllMachines($limit);
        }
    }

    public function getMachineById(int $id)
    {
        return $this->repository->findMachine($id);
    }

    public function findOrFail(int $id)
    {
        return $this->repository->checkMachineExist($id);
    }

    public function createMachine(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateMachine(int $id, array $data): bool
    {
        if (!$this->repository->checkMachineExist($id)) {
            return false;
        }
        return $this->repository->update($id, $data);
    }

    public function deleteMachine(int $id): bool
    {
        return $this->repository->delete($id);
    }
}