<?php

namespace App\Services;

use App\Repositories\TrucksRepository;

class TrucksService
{
    protected TrucksRepository $repository;
    public function __construct(TrucksRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllTrucks(int $companyId, int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedTrucks($companyId, $limit, $search);
        }

        return $this->repository->getAllTrucks($companyId, $limit);
    }
    //---------------
    public function getTruckById(int $id, int $companyId)
    {
        return $this->repository->findTruck($id, $companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId)
    {
        return $this->repository->checkTruckExist($id, $companyId);
    }
    //---------------
    public function createTruck(array $data, int $companyId)
    {
        return $this->repository->create($data, $companyId);
    }
    //---------------
    public function updateTruck(int $id, array $data, int $companyId): bool
    {
        $truck = $this->repository->findTruck($id, $companyId);
        if (!$truck) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteTruck(int $id, int $companyId): bool
    {
        $truck = $this->repository->findTruck($id, $companyId);
        if (!$truck) {
            return false;
        }
        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function changeTruckStatus(int $id, int $companyId): bool
    {
        $truck = $this->repository->findTruck($id, $companyId);
        if (!$truck) {
            return false;
        }
        $newStatus = $truck->status === 'Free' ? 'Busy' : 'Free';
        return $this->repository->changeStatus($id, $companyId, $newStatus);
    }
}
