<?php

namespace App\Services;

use App\Repositories\StaffRepository;

class StaffService
{
    public function __construct(
        protected StaffRepository $repository
    ) {}

    public function getAllStaff(int $limit, ?int $companyId = null)
    {
        if (isset($_GET['search']) && ! empty($_GET['search'])) {
            return $this->repository->getSearchedStaff($_GET['search'], $limit, $companyId);
        } else {
            return $this->repository->getAllStaff($limit, $companyId);
        }
    }

    public function getStaffById(int $id, ?int $companyId = null)
    {
        return $this->repository->findStaffById($id, $companyId);
    }

    public function checkStaffExist(int $id, ?int $companyId = null): bool
    {
        return $this->repository->checkStaffExist($id, $companyId);
    }

    public function createStaff(array $data): bool
    {
        return $this->repository->create($data);
    }

    public function updateStaff(int $id, array $data, ?int $companyId = null): bool
    {
        $staff = $this->repository->findStaffById($id, $companyId);

        if (! $staff) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }

    public function deleteStaff(int $id, ?int $companyId = null): bool
    {
        $staff = $this->repository->findStaffById($id, $companyId);

        if (! $staff) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }
}
