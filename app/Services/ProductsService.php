<?php

namespace App\Services;

use App\Repositories\ProductsRepository;

class ProductsService
{
    public function __construct(
        protected ProductsRepository $repository
    ) {}

    public function getAllProducts(int $limit, ?int $companyId = null)
    {
        if (isset($_GET['search']) && ! empty($_GET['search'])) {
            return $this->repository->getSearchedProducts($_GET['search'], $limit, $companyId);
        } else {
            return $this->repository->getAllProducts($limit, $companyId);
        }
    }

    public function getProductsById(int $id, ?int $companyId = null)
    {
        return $this->repository->findProductsById($id, $companyId);
    }

    public function checkProductsExist(int $id, ?int $companyId = null): bool
    {
        return $this->repository->checkProductsExist($id, $companyId);
    }

    public function createProducts(array $data): bool
    {
        return $this->repository->create($data);
    }

    public function hasDuplicateProduct(
        string $product,
        string $unit,
        int $companyId,
        ?int $excludeId = null
    ): bool {
        return $this->repository->hasDuplicateProduct($product, $unit, $companyId, $excludeId);
    }

    public function updateProducts(int $id, array $data, ?int $companyId = null): bool
    {
        $products = $this->repository->findProductsById($id, $companyId);

        if (! $products) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }

    public function deleteProducts(int $id, ?int $companyId = null): bool
    {
        $products = $this->repository->findProductsById($id, $companyId);

        if (! $products) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }
}
