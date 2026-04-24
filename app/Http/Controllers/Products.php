<?php

namespace App\Http\Controllers;

use App\Services\ProductsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Products extends Controller
{
    public function __construct(
        protected ProductsService $service
    ) {}

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product' => 'required|string|min:1|max:255',
            'unit' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:0',
            'company_id' => 'required|integer|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $request->only(['product', 'unit', 'price', 'company_id']);

        if ($this->service->createProducts($payload)) {
            return response()->json([
                'success' => true,
                'message' => 'Product registered successfully.',
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while saving the product data. Please try again.',
        ], 500);
    }

    public function read(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'company_id' => 'required|integer|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'company_id is required for listing products data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = (int) $request->query('company_id');

        return $this->service->getAllProducts(10, $companyId);
    }

    public function edit(Request $request, int $id)
    {
        $validator = Validator::make($request->query(), [
            'company_id' => 'required|integer|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'company_id is required.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = (int) $request->query('company_id');

        if (! $this->service->checkProductsExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return $this->service->getProductsById($id, $companyId);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pid' => 'required|integer|exists:products,pid',
            'company_id' => 'required|integer|exists:companies,cid',
            'product' => 'required|string|min:1|max:255',
            'unit' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = (int) $request->input('company_id');
        $pid = (int) $request->input('pid');

        if (! $this->service->checkProductsExist($pid, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found for this company.',
            ], 404);
        }

        $data = $request->only(['product', 'unit', 'price']);

        $updated = $this->service->updateProducts($pid, $data, $companyId);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Product updated.' : 'Update failed.',
        ], $updated ? 200 : 500);
    }

    public function delete(Request $request, int $id)
    {
        $validator = Validator::make($request->query(), [
            'company_id' => 'required|integer|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'company_id is required.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = (int) $request->query('company_id');

        if (! $this->service->checkProductsExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $deleted = $this->service->deleteProducts($id, $companyId);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Product deleted.' : 'Delete failed.',
        ], $deleted ? 200 : 500);
    }
}
