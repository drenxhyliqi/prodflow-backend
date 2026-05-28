<?php

namespace App\Services;

use App\Repositories\ProductsRepository;
use Illuminate\Support\Facades\Cache;

class ProductsService
{
    protected ProductsRepository $repository;
    public function __construct(ProductsRepository $repository)
    {
        $this->repository = $repository;
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
        $result = $this->repository->create($data, $companyId);
        if ($result) {
            Cache::tags(['products'])->flush();
        }
        return $result;
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

        $result = $this->repository->update($id, $data, $companyId);
        if ($result) {
            Cache::tags(['products'])->flush();
        }
        return $result;
    }
    //---------------
    public function deleteProducts(int $id, int $companyId): bool
    {
        $products = $this->repository->findProductsById($id, $companyId);

        if (! $products) {
            return false;
        }

        $result = $this->repository->delete($id, $companyId);
        if ($result) {
            Cache::tags(['products'])->flush();
        }
        return $result;
    }
}
