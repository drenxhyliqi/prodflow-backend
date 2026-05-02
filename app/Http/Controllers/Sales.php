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
            'products' => 'required|array|min:1',
            'products.*.product' => 'required|string|min:1|max:255',
            'products.*.qty' => 'required|numeric|min:1',
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
        if (!$this->service->findOrFail($sale_number, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Sale not found.'
            ], 404);
        } else {
            return $this->service->getSaleById($sale_number, $companyId);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sid' => 'required|numeric|min:1|exists:sales,sid',
            'client' => 'required|string|min:1|max:255',
            'product' => 'required|string|min:1|max:255',
            'qty' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:1',
            'total' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $id = $request->sid;
            $data = $request->only(['sale_number', 'client', 'product', 'qty', 'price']);
            $companyId = $request->user()->company_id;
            if ($this->service->updateSale($id, $data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'The sale was successfully updated.'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No data was updated.'
                ], 500);
            }
        }
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
