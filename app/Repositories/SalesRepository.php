<?php

namespace App\Repositories;

use App\Models\SalesModel;
use Illuminate\Support\Facades\DB;

class SalesRepository
{
    protected string $table;
    public function __construct(SalesModel $model)
    {
        $this->table = $model->getTable();

    }
    //---------------
    public function getAllSales(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->select('sale_number', 'client', 'date')
            ->where('company_id', $companyId)
            ->groupBy('sale_number', 'client', 'date')
            ->orderByDesc('date')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedSales(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->select('sale_number', 'client', 'date')
            ->where('company_id', $companyId)
            ->where('sale_number', 'like', "%{$search}%")
            ->groupBy('sale_number', 'client', 'date')
            ->orderByDesc('date')
            ->paginate($limit);
    }
    //---------------
    public function findSaleByNumber(string $sale_number, int $companyId)
    {
        return DB::table($this->table)
            ->join('products', 'sales.product_id', '=', 'products.pid')
            ->select('sales.sale_number', 'sales.client', 'sales.product_id', 'sales.qty', 'sales.price', 'sales.total', 'sales.date', 'products.product', 'products.unit')
            ->where('sales.sale_number', $sale_number)
            ->where('sales.company_id', $companyId)
            ->get();
    }
    //---------------
    public function checkSaleExist(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sid', $id)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function findSalesByProductId(int $id, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('product_id', $id)
            ->where('company_id', $companyId)
            ->exists();
    }
    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)
            ->insert($data);
    }
    //---------------
    public function update(string $sale_number, array $data, int $companyId): bool
    {
        DB::beginTransaction();
        try {
            DB::table($this->table)
                ->where('sale_number', $sale_number)
                ->where('company_id', $companyId)
                ->delete();

            foreach ($data['products'] as $product) {
                DB::table($this->table)->insert([
                    'sale_number' => $sale_number,
                    'client' => $data['client'],
                    'product_id' => $product['product_id'],
                    'qty' => $product['qty'],
                    'price' => $product['price'],
                    'total' => $product['total_price'],
                    'company_id' => $companyId,
                    'date' => now(),
                ]);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    //---------------
    public function delete(string $sale_number, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('sale_number', $sale_number)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }
    //---------------
    public function findInvoiceInformations(string $sale_number, int $companyId)
    {
        return DB::table($this->table)
            ->join('products', 'sales.product_id', '=', 'products.pid')
            ->select('sales.sale_number', 'sales.client', 'sales.qty', 'sales.price', 'sales.total', 'sales.date', 'products.product', 'products.unit')
            ->where('sales.sale_number', $sale_number)
            ->where('sales.company_id', $companyId)
            ->get();
    }
}
