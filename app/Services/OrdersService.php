<?php

namespace App\Services;

use App\Repositories\OrdersRepository;
use App\Repositories\ProductsRepository;
use App\Repositories\SalesRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersService
{
    protected OrdersRepository $repository;
    protected ProductsRepository $productsRepository;
    protected SalesRepository $salesRepository;

    public function __construct(
        OrdersRepository $repository,
        ProductsRepository $productsRepository,
        SalesRepository $salesRepository
    ) {
        $this->repository = $repository;
        $this->productsRepository = $productsRepository;
        $this->salesRepository = $salesRepository;
    }

    //---------------
    public function getAllOrders(int $companyId, int $limit, string $search = '')
    {
        $limit = (int) $limit ?: 10;

        if (! empty($search)) {
            return $this->repository->getSearchedOrders($companyId, $limit, $search);
        }

        return $this->repository->getAllOrders($companyId, $limit);
    }

    //---------------
    public function getOrderByNumber(string $orderNumber, int $companyId): ?array
    {
        $order = $this->repository->findOrder($orderNumber, $companyId);
        if (! $order) {
            return null;
        }

        $items = $this->repository->getOrderItems($orderNumber, $companyId);

        return [
            'order_number' => $order->order_number,
            'client' => $order->client,
            'status' => $order->status,
            'sale_number' => $order->sale_number,
            'date' => $order->date,
            'items' => $items,
            'total' => round((float) $items->sum('total'), 2),
        ];
    }

    //---------------
    public function findOrFail(string $orderNumber, int $companyId): bool
    {
        return $this->repository->checkOrderExist($orderNumber, $companyId);
    }

    //---------------
    public function createOrder(array $data, int $companyId): bool
    {
        return DB::transaction(function () use ($data, $companyId) {
            $orderNumber = $this->generateOrderNumber($companyId);

            return $this->repository->create(
                $this->buildOrderRows($data, $companyId, $orderNumber)
            );
        });
    }

    //---------------
    public function updateOrder(string $orderNumber, array $data, int $companyId): bool
    {
        $order = $this->repository->findOrder($orderNumber, $companyId);
        if (! $order) {
            return false;
        }

        if ($order->status !== 'pending') {
            throw new \Exception('Only pending orders can be updated.');
        }

        return DB::transaction(function () use ($orderNumber, $data, $companyId) {
            $this->repository->delete($orderNumber, $companyId);

            return $this->repository->create(
                $this->buildOrderRows($data, $companyId, $orderNumber)
            );
        });
    }

    //---------------
    public function deleteOrder(string $orderNumber, int $companyId): bool
    {
        $order = $this->repository->findOrder($orderNumber, $companyId);
        if (! $order) {
            return false;
        }

        if ($order->status === 'completed') {
            throw new \Exception('Completed orders cannot be deleted because they are already linked to sales.');
        }

        return $this->repository->delete($orderNumber, $companyId);
    }

    //---------------
    public function convertToSale(string $orderNumber, int $companyId): ?string
    {
        $order = $this->repository->findOrder($orderNumber, $companyId);
        if (! $order) {
            return null;
        }

        if ($order->status === 'completed' && ! empty($order->sale_number)) {
            return $order->sale_number;
        }

        return DB::transaction(function () use ($orderNumber, $companyId) {
            $rows = $this->repository->getOrderRowsForConversion($orderNumber, $companyId);
            if ($rows->isEmpty()) {
                return null;
            }

            $saleNumber = strtoupper(Str::random(6));

            $saleRows = $rows->map(function ($row) use ($saleNumber, $companyId) {
                return [
                    'sale_number' => $saleNumber,
                    'client' => $row->client,
                    'product_id' => $row->product_id,
                    'qty' => $row->qty,
                    'price' => $row->price,
                    'total' => $row->total,
                    'company_id' => $companyId,
                    'date' => now()->toDateString(),
                ];
            })->toArray();

            $created = $this->salesRepository->create($saleRows);
            if (! $created) {
                throw new \Exception('Order could not be converted to sale.');
            }

            $this->repository->markAsCompleted($orderNumber, $companyId, $saleNumber);

            return $saleNumber;
        });
    }

    //---------------
    private function buildOrderRows(
        array $data,
        int $companyId,
        string $orderNumber
    ): array {
        $rows = [];

        foreach ($data['products_id'] as $item) {
            $product = $this->productsRepository->findProductsById(
                $item['products_id'],
                $companyId
            );

            if (! $product) {
                throw new \Exception('Product with ID :' . $item['products_id'] . ' does not exist.');
            }

            $rows[] = [
                'order_number' => $orderNumber,
                'client' => $data['client'],
                'product_id' => $product->pid,
                'qty' => $item['qty'],
                'price' => $product->price,
                'total' => $product->price * $item['qty'],
                'status' => 'pending',
                'sale_number' => null,
                'company_id' => $companyId,
                'date' => now()->toDateString(),
            ];
        }

        return $rows;
    }

    //---------------
    private function generateOrderNumber(int $companyId): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(Str::random(6));
        } while ($this->repository->checkOrderExist($orderNumber, $companyId));

        return $orderNumber;
    }
}
