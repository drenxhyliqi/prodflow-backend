<?php

namespace App\Http\Controllers;

use App\Services\SuppliersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Suppliers extends Controller
{
    protected SuppliersService $service;
    public function __construct(SuppliersService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $data = $request->only(['supplier', 'phone', 'location']);
            $companyId = $request->user()->company_id;
            if ($this->service->createSupplier($data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Supplier registered successfully.'
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
    public function read(Request $request)
    {
        $search = $request->query('search', '');
        $companyId = $request->user()->company_id;
        return response()->json(
            $this->service->getAllSuppliers($companyId, 10, $search)
        );
    }
    //---------------
    public function edit(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if (!$this->service->findOrFail($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found.'
            ], 404);
        } else {
            return $this->service->getSupplierById($id, $companyId);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sid' => 'required|numeric|min:1|exists:suppliers,sid',
            'supplier' => 'required|string|min:1|max:255',
            'phone' => 'required|string|min:1|max:255',
            'location' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $id = $request->sid;
            $data = $request->only(['supplier', 'phone', 'location']);
            $companyId = $request->user()->company_id;
            if ($this->service->updateSupplier($id, $data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'The supplier was successfully updated.'
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
    public function delete(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if ($this->service->deleteSupplier($id, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The supplier was successfully deleted.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
}
