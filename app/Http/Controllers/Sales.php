<?php

namespace App\Http\Controllers;

use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Sales extends Controller
{
    protected SalesService $service;
    public function __construct(SalesService $service)
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
            'products_id.*.qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            try {
                $data = $validator->validated();
                $companyId = $request->user()->company_id;
                if ($this->service->createSale($data, $companyId)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Sale registered successfully.'
                    ], 201);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'An error occurred while saving the data. Please try again.'
                    ], 500);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $search = $request->query('search', '');
        $companyId = $request->user()->company_id;
        return response()->json(
            $this->service->getAllSales($companyId, 10, $search)
        );
    }
    //---------------
    public function edit(Request $request, string $sale_number)
    {
        $companyId = $request->user()->company_id;
        if (!$this->service->getSaleByNumber($sale_number, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Sale not found.'
            ], 404);
        } else {
            return $this->service->getSaleByNumber($sale_number, $companyId);
        }
    }
    //---------------
    public function update(Request $request, string $sale_number)
    {
        $validator = Validator::make($request->all(), [
            'client' => 'required|string|min:1|max:255',
            'total_price' => 'required|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|min:1|exists:products,pid',
            'products.*.qty' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.total_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        }
        $companyId = $request->user()->company_id;
        $data = [
            'sale_number' => $sale_number,
            'client' => $request->client,
            'total_price' => $request->total_price,
            'products' => collect($request->products)->map(function ($item) {
                return [
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total_price' => $item['total_price'],
                ];
            })->toArray()
        ];
        if ($this->service->updateSale($sale_number, $data, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The sale was successfully updated.'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'No data was updated.'
        ], 500);
    }
    //---------------
    public function delete(Request $request, string $sale_number)
    {
        $companyId = $request->user()->company_id;
        if ($this->service->deleteSale($sale_number, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The sale was successfully deleted.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
}
