<?php

namespace App\Services;

use App\Repositories\SalesRepository;

class SalesService
{
    protected SalesRepository $repository;
    public function __construct(SalesRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getAllSales($limit)
    {
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            return $this->repository->getSearchedSales($_GET['search'], $limit);
        } else {
            return $this->repository->getAllSales($limit);
        }
    }
    //---------------
    public function getSaleById(int $id)
    {
        return $this->repository->findSale($id);
    }
    //---------------
    public function findOrFail(int $id)
    {
        return $this->repository->checkSaleExist($id);
    }
    //---------------
    public function createSale(array $data)
    {
        return $this->repository->create($data);
    }
    //---------------
    public function updateSale(int $id, array $data): bool
    {
        $sale = $this->repository->findSale($id);
        if (!$sale) {
            return false;
        }
        return $this->repository->update($id, $data);
    }
    //---------------
    public function deleteSale(int $id): bool
    {
        return $this->repository->delete($id);
    }
    //---------------
}
