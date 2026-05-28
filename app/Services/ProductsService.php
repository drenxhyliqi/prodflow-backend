<?php

namespace App\Services;

use App\Repositories\ProductsRepository;
use App\Repositories\ProductionRepository;
use App\Repositories\SalesRepository;
use Exception;
use Illuminate\Support\Facades\Cache;

class ProductsService
{
    protected ProductsRepository $repository;
    protected ProductionRepository $productionRepository;
    protected SalesRepository $salesRepository;
    public function __construct(ProductsRepository $repository, ProductionRepository $productionRepository, SalesRepository $salesRepository)
    {
        $this->repository = $repository;
        $this->productionRepository = $productionRepository;
        $this->salesRepository = $salesRepository;
    }
    //---------------
    public function getAllProducts(int $limit, int $companyId, string $search, int $page)
    {
        if (!empty($search)) {
            return $this->repository->getSearchedProducts($search, $limit, $companyId);
        }

        $cacheKey = "products_company_{$companyId}_page_{$page}";
        return Cache::tags(['products'])->remember(
            $cacheKey,
            now()->addHours(3),
            function () use ($limit, $companyId) {
                return $this->repository->getAllProducts($limit, $companyId);
            }
        );
    }
    //---------------
    public function getProductsById(int $id, int $companyId)
    {
        return $this->repository->findProductsById($id, $companyId);
    }
    //---------------
    public function checkProductsExist(int $id, int $companyId): bool
    {
        return $this->repository->checkProductsExist($id, $companyId);
    }
    //---------------
    public function createProducts(array $data, int $companyId): bool
    {
        Cache::tags(['products'])->flush();
        return $this->repository->create($data, $companyId);
    }
    //---------------
    public function hasDuplicateProduct(
        string $product,
        string $unit,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return $this->repository->hasDuplicateProduct($product, $unit, $companyId, $excludeId);
    }
    //---------------
    public function updateProducts(int $id, array $data, int $companyId): bool
    {
        $products = $this->repository->findProductsById($id, $companyId);

        if (! $products) {
            return false;
        }
        Cache::tags(['products'])->flush();
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteProducts(int $id, int $companyId): bool
    {
        $products = $this->repository->findProductsById($id, $companyId);
        if (! $products) {
            throw new Exception('Product not found.');
        }
        $sales = $this->salesRepository->findSalesByProductId($id, $companyId);
        if ($sales) {
            throw new Exception('You cannot delete this product because it has related sales data.');
        }
        $production = $this->productionRepository->findProductionByProductId($id, $companyId);
        if ($production) {
            throw new Exception('You cannot delete this product because it has related production data.');
        }
        Cache::tags(['products'])->flush();
        return $this->repository->delete($id, $companyId);
    }
}
