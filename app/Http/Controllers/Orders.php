<?php

namespace App\Http\Controllers;

use App\Services\OrdersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Orders extends Controller
{
    protected OrdersService $service;

    public function __construct(OrdersService $service)
    {
        $this->service = $service;
    }

    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client' => 'required|string|min:1|max:255',
            'products_id' => 'required|array|min:1',
            'products_id.*.products_id' => 'required|integer',
            'products_id.*.qty' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $companyId = $request->user()->company_id;
            $created = $this->service->createOrder($validator->validated(), $companyId);

            return response()->json([
                'success' => $created,
                'message' => $created
                    ? 'Order registered successfully.'
                    : 'An error occurred while saving the data. Please try again.',
            ], $created ? 201 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    //---------------
    public function read(Request $request)
    {
        $search = $request->query('search', '');
        $companyId = $request->user()->company_id;

        return response()->json(
            $this->service->getAllOrders($companyId, 10, $search)
        );
    }

    //---------------
    public function edit(Request $request, string $order_number)
    {
        $companyId = $request->user()->company_id;
        $order = $this->service->getOrderByNumber($order_number, $companyId);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json($order);
    }

    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => 'required|string|max:20',
            'client' => 'required|string|min:1|max:255',
            'products_id' => 'required|array|min:1',
            'products_id.*.products_id' => 'required|integer',
            'products_id.*.qty' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $companyId = $request->user()->company_id;
            $updated = $this->service->updateOrder(
                $request->order_number,
                $validator->validated(),
                $companyId
            );

            return response()->json([
                'success' => $updated,
                'message' => $updated ? 'The order was successfully updated.' : 'Order not found.',
            ], $updated ? 200 : 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    //---------------
    public function delete(Request $request, string $order_number)
    {
        try {
            $companyId = $request->user()->company_id;
            $deleted = $this->service->deleteOrder($order_number, $companyId);

            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? 'The order was successfully deleted.' : 'Order not found.',
            ], $deleted ? 200 : 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    //---------------
    public function convertToSale(Request $request, string $order_number)
    {
        try {
            $companyId = $request->user()->company_id;
            $saleNumber = $this->service->convertToSale($order_number, $companyId);

            if (! $saleNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order converted to sale successfully.',
                'sale_number' => $saleNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
