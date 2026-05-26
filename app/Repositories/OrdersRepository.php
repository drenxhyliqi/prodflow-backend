<?php

namespace App\Repositories;

use App\Models\OrdersModel;
use Illuminate\Support\Facades\DB;

class OrdersRepository
{
    protected string $table;

    public function __construct(OrdersModel $model)
    {
        $this->table = $model->getTable();
    }

    //---------------
    public function getAllOrders(int $companyId, int $limit)
    {
        return DB::table($this->table)
            ->select(
                'order_number',
                'client',
                'status',
                'sale_number',
                'date',
                DB::raw('SUM(total) as grand_total')
            )
            ->where('company_id', $companyId)
            ->groupBy('order_number', 'client', 'status', 'sale_number', 'date')
            ->orderByDesc('date')
            ->paginate($limit);
    }

    //---------------
    public function getSearchedOrders(int $companyId, int $limit, string $search)
    {
        return DB::table($this->table)
            ->select(
                'order_number',
                'client',
                'status',
                'sale_number',
                'date',
                DB::raw('SUM(total) as grand_total')
            )
            ->where('company_id', $companyId)
            ->where(function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            })
            ->groupBy('order_number', 'client', 'status', 'sale_number', 'date')
            ->orderByDesc('date')
            ->paginate($limit);
    }

    //---------------
    public function findOrder(string $orderNumber, int $companyId)
    {
        return DB::table($this->table)
            ->select('order_number', 'client', 'status', 'sale_number', 'date')
            ->where('order_number', $orderNumber)
            ->where('company_id', $companyId)
            ->first();
    }

    //---------------
    public function getOrderItems(string $orderNumber, int $companyId)
    {
        return DB::table($this->table)
            ->join('products', 'orders.product_id', '=', 'products.pid')
            ->select(
                'orders.oid',
                'orders.order_number',
                'orders.client',
                'orders.product_id',
                'orders.qty',
                'orders.price',
                'orders.total',
                'orders.status',
                'orders.sale_number',
                'orders.date',
                'products.product',
                'products.unit'
            )
            ->where('orders.order_number', $orderNumber)
            ->where('orders.company_id', $companyId)
            ->orderBy('orders.oid')
            ->get();
    }

    //---------------
    public function getOrderRowsForConversion(string $orderNumber, int $companyId)
    {
        return DB::table($this->table)
            ->where('order_number', $orderNumber)
            ->where('company_id', $companyId)
            ->orderBy('oid')
            ->get();
    }

    //---------------
    public function checkOrderExist(string $orderNumber, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('order_number', $orderNumber)
            ->where('company_id', $companyId)
            ->exists();
    }

    //---------------
    public function create(array $data): bool
    {
        return DB::table($this->table)->insert($data);
    }

    //---------------
    public function delete(string $orderNumber, int $companyId): bool
    {
        return DB::table($this->table)
            ->where('order_number', $orderNumber)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }

    //---------------
    public function markAsCompleted(string $orderNumber, int $companyId, string $saleNumber): bool
    {
        return DB::table($this->table)
            ->where('order_number', $orderNumber)
            ->where('company_id', $companyId)
            ->update([
                'status' => 'completed',
                'sale_number' => $saleNumber,
            ]) > 0;
    }
}
