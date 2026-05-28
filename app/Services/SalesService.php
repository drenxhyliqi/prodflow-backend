<?php

namespace App\Services;

use App\Repositories\SalesRepository;
use App\Repositories\ClientsRepository;
use App\Repositories\ProductsRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesService
{
    protected SalesRepository $repository;
    protected ClientsRepository $clientsrepository;
    protected ProductsRepository $productsrepository;
    public function __construct(SalesRepository $repository, ClientsRepository $clientsrepository, ProductsRepository $productsrepository)
    {
        $this->repository = $repository;
        $this->clientsrepository = $clientsrepository;
        $this->productsrepository = $productsrepository;
    }
    //---------------
    public function getAllSales(int $companyId, int $limit, string $search, int $page)
    {
        if (!empty($search)) {
            return $this->repository->getSearchedSales($companyId, $limit, $search);
        }

        $cacheKey = "sales_company_{$companyId}_page_{$page}";
        return Cache::tags(['sales'])->remember(
            $cacheKey,
            now()->addHours(3),
            function () use ($companyId, $limit) {
                return $this->repository->getAllSales($companyId, $limit);
            }
        );
    }
    //---------------
    public function getSaleByNumber(string $sale_number, int $companyId)
    {
        return $this->repository->findSaleByNumber($sale_number, $companyId);
    }
    //---------------
    public function findOrFail(int $id, int $companyId)
    {
        return $this->repository->checkSaleExist($id, $companyId);
    }
    //---------------
    public function createSale(array $data, int $companyId)
    {
        return DB::transaction(function () use ($data, $companyId) {
            $saleNumber = strtoupper(Str::random(6));
            $saleProducts = [];
            foreach ($data['products_id'] as $item) {
                $product = $this->productsrepository->findProductsById(
                    $item['products_id'],
                    $companyId
                );
                if (!$product) {
                    throw new \Exception('Product with ID :' . $item['products_id'] .' does not exist.');
                }
                $saleProducts[] = [
                    'sale_number' => $saleNumber,
                    'client' => $data['client'],
                    'product_id' => $product->pid,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'total' => $product->price * $item['qty'],
                    'company_id' => $companyId,
                    'date' => now()
                ];
            }
            Cache::tags(['sales'])->flush();
            return $this->repository->create($saleProducts);
        });
    }
    //---------------
    public function updateSale(string $sale_number, array $data, int $companyId): bool
        {
            $sale = $this->repository->findSaleByNumber($sale_number, $companyId);
            if (!$sale) {
                return false;
            }
            Cache::tags(['sales'])->flush();
            return $this->repository->update(
                $sale_number,
                $data,
                $companyId
            );
        }
    //---------------
    public function deleteSale(string $sale_number, int $companyId): bool
    {
        $company = $this->repository->findSaleByNumber($sale_number, $companyId);
        if (!$company) {
            return false;
        }
        Cache::tags(['sales'])->flush();
        return $this->repository->delete($sale_number, $companyId);
    }
    //---------------
    public function getInvoiceInformations(string $sale_number, int $companyId)
    {
        return $this->repository->findInvoiceInformations($sale_number, $companyId);
    }
}
