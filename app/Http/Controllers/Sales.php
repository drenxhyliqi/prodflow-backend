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
            'client_id' => 'required|numeric|exists:clients,cid',
            'product_id' => 'required|numeric|exists:products,pid',
            'qty' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'company_id' => 'required|numeric|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            if ($this->service->createSale($request->only(['client_id', 'product_id', 'qty', 'price', 'date', 'company_id']))) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale created successfully.'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving the data. Please try again.'
                ], 500);
            }
        }
    }
    //---------------
    public function read()
    {
        return $this->service->getAllSales(10);
    }
    //---------------
    public function edit($id)
    {
        if (!$this->service->findOrFail($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Sale not found.'
            ], 404);
        } else {
            return $this->service->getSaleById($id);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sid' => 'required|numeric|min:1|exists:sales,sid',
            'client_id' => 'required|numeric|exists:clients,cid',
            'product_id' => 'required|numeric|exists:products,pid',
            'qty' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'company_id' => 'required|numeric|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            return $this->service->updateSale($request->input('id'), $request->only(['client_id', 'product_id', 'qty', 'price', 'date', 'company_id']));
        }
    }
    //---------------
    public function delete($id)
    {
        return $this->service->deleteSale($id);
    }
}
