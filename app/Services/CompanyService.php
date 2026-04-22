<?php

namespace App\Services;

use App\Repositories\CompanyRepository;

class CompanyService
{
    protected CompanyRepository $repository;
    public function __construct(CompanyRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllCompanies($limit)
    {
        if(isset($_GET['search']) && !empty($_GET['search'])){
            return $this->repository->getSearchedCompanies($_GET['search'], $limit);
        }else{
            return $this->repository->getAllCompanies($limit);
        }
    }
    //---------------
    public function getCompanyById(int $id)
    {
        return $this->repository->findCompany($id);
    }
    //---------------
    public function findOrFail(int $id)
    {
        return $this->repository->checkCompanyExist($id);
    }
    //---------------
    public function createCompany(array $data)
    {
        return $this->repository->create($data);
    }
    //---------------
    public function updateCompany(int $id, array $data): bool
    {
        $company = $this->repository->findCompany($id);
        if (!$company) {
            return false;
        }
        return $this->repository->update($id, $data);
    }
    //---------------
    public function deleteCompany(int $id): bool
    {
        return $this->repository->delete($id);
    }
    //---------------
    public function activateCompany(int $id): bool
    {
        return $this->repository->changeStatus($id, [
            'status' => 'Active'
        ]);
    }
    //---------------
    public function deactivateCompany(int $id): bool
    {
        return $this->repository->changeStatus($id, [
            'status' => 'Deactive'
        ]);
    }
}
