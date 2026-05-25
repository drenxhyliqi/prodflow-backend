<?php

namespace App\Services;

use App\Repositories\ReportsRepository;

class ReportsService
{
    protected ReportsRepository $repository;
    public function __construct(ReportsRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getProductsStock(int $limit, int $companyId, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedProductsStock($search, $companyId, $limit);
        }
        return $this->repository->getProductsStock($limit, $companyId);
    }
}
