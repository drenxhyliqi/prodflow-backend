<?php

namespace App\Services;

use App\Repositories\SalesRepository;
use App\Repositories\ClientsRepository;
use App\Repositories\ProductsRepository;
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
    public function getAllSales(int $companyId, int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;
        if (!empty($search)) {
            return $this->repository->getSearchedSales($companyId, $limit, $search);
        }

        return $this->repository->getAllSales($companyId, $limit);
    }
    //---------------
    public function getSaleById(int $id, int $companyId)
    {
        return $this->repository->findSale($id, $companyId);
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
            foreach ($data['products'] as $item) {
                $product = $this->productsrepository->findProductByName(
                    $item['product'],
                    $companyId
                );
                if (!$product) {
                    throw new \Exception('Product does not exist: ' . $item['product']);
                }
                $saleProducts[] = [
                    'sale_number' => $saleNumber,
                    'client' => $data['client'],
                    'product' => $product->product,
                    'unit' => $product->unit,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'total' => $product->price * $item['qty'],
                    'company_id' => $companyId,
                    'date' => now()
                ];
            }
            return $this->repository->create($saleProducts);
        });
    }
    //---------------
    public function updateSale(int $id, array $data, int $companyId): bool
    {
        $sale = $this->repository->findSale($id, $companyId);
        if (!$sale) {
            return false;
        }
        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteSale(string $sale_number, int $companyId): bool
    {
        $company = $this->repository->findSale($sale_number, $companyId);
        if (!$company) {
            return false;
        }
        return $this->repository->delete($sale_number, $companyId);
    }
}
