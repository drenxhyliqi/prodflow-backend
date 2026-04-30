<?php

namespace App\Http\Controllers;

use App\Services\WarehousesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Warehouses extends Controller
{
    protected WarehousesService $service;

    public function __construct(WarehousesService $service)
    {
        $this->service = $service;
    }

    //==================
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'warehouse' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $data = $request->only(['warehouse', 'location']);
            $companyId = $request->user()->company_id;

            if ($this->service->createWarehouse($data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Warehouse registered successfully.'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving the data. Please try again.'
                ], 500);
            }
        }
    }

    //==================
    public function read(Request $request)
    {
        $search = $request->query('search', '');
        $companyId = $request->user()->company_id;
        
        return response()->json(
            $this->service->getAllWarehouses($companyId, 10, $search)
        );
    }

    //==================
    public function edit(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if (!$this->service->findOrFail($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found.'
            ], 404);
        } else {
            return $this->service->getWarehouseById($id, $companyId);
        }
    }

    //==================
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wid' => 'required|numeric|min:1|exists:warehouses,wid',
            'warehouse' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $id = $request->wid;
            $data = $request->only(['warehouse', 'location']);
            $companyId = $request->user()->company_id;

            if ($this->service->updateWarehouse($id, $data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'The warehouse was successfully updated.'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No data was updated.'
                ], 500);
            }
        }
    }

    //==================
    public function delete(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if ($this->service->deleteWarehouse($id, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The warehouse was successfully deleted.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
}