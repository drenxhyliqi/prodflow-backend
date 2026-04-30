<?php

namespace App\Services;

use App\Repositories\StaffRepository;

class StaffService
{
    protected StaffRepository $repository;
    public function __construct(StaffRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllStaff(int $limit, int $companyId, string $search = '')
    {
        if (! empty($search)) {
            return $this->repository->getSearchedStaff($search, $limit, $companyId);
        } else {
            return $this->repository->getAllStaff($limit, $companyId);
        }
    }
    //---------------
    public function getStaffById(int $id, int $companyId)
    {
        return $this->repository->findStaffById($id, $companyId);
    }
    //---------------
    public function checkStaffExist(int $id, int $companyId): bool
    {
        return $this->repository->checkStaffExist($id, $companyId);
    }
    //---------------
    public function createStaff(array $data, int $companyId): bool
    {
        return $this->repository->create($data, $companyId);
    }
    //---------------
    public function hasDuplicateStaff(
        string $name,
        string $surname,
        string $position,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return $this->repository->hasDuplicateStaff($name, $surname, $position, $companyId, $excludeId);
    }
    //---------------
    public function updateStaff(int $id, array $data, int $companyId): bool
    {
        $staff = $this->repository->findStaffById($id, $companyId);

        if (! $staff) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteStaff(int $id, int $companyId): bool
    {
        $staff = $this->repository->findStaffById($id, $companyId);

        if (! $staff) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }
}
