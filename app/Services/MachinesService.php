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

    //---------------
    public function getAllMachines(int $companyId, int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;

        if (!empty($search)) {
            return $this->repository->getSearchedMachines($companyId, $limit, $search);
        }

        return $this->repository->getAllMachines($companyId, $limit);
    }

    //---------------
    public function getMachineById(int $id, int $companyId)
    {
        return $this->repository->findMachine($id, $companyId);
    }

    //---------------
    public function findOrFail(int $id, int $companyId)
    {
        return $this->repository->checkMachineExist($id, $companyId);
    }

    //---------------
    public function createMachine(array $data, int $companyId)
    {
        return $this->repository->create($data, $companyId);
    }

    //---------------
    public function updateMachine(int $id, array $data, int $companyId): bool
    {
        $machine = $this->repository->findMachine($id, $companyId);
        if (!$machine) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }

    //---------------
    public function deleteMachine(int $id, int $companyId): bool
    {
        $machine = $this->repository->findMachine($id, $companyId);
        if (!$machine) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
}