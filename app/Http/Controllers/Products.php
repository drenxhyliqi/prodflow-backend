<?php

namespace App\Http\Controllers;

use App\Services\ProductsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Products extends Controller
{
    protected ProductsService $service;
    public function __construct(ProductsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'product' => 'required|string|min:1|max:255',
            'unit' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $request->only(['product', 'unit', 'price']);
        $payload['company_id'] = (int) $user->company_id;

        if ($this->service->hasDuplicateProduct(
            $payload['product'],
            $payload['unit'],
            $payload['company_id']
        )) {
            return response()->json([
                'success' => false,
                'message' => 'This product already exists.',
            ], 409);
        }

        if ($this->service->createProducts($payload, $payload['company_id'])) {
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
    //---------------
    public function read(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $companyId = (int) $user->company_id;

        return $this->service->getAllProducts(10, $companyId);
    }
    //---------------
    public function edit(Request $request, int $id)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $companyId = (int) $user->company_id;

        if (! $this->service->checkProductsExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return $this->service->getProductsById($id, $companyId);
    }
    //---------------
    public function update(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'pid' => 'required|integer|exists:products,pid',
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

        $companyId = (int) $user->company_id;
        $pid = (int) $request->input('pid');

        if (! $this->service->checkProductsExist($pid, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found for this company.',
            ], 404);
        }

        $data = $request->only(['product', 'unit', 'price']);

        if ($this->service->hasDuplicateProduct(
            $data['product'],
            $data['unit'],
            $companyId,
            $pid
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Another product with the same details already exists.',
            ], 409);
        }

        $updated = $this->service->updateProducts($pid, $data, $companyId);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Product updated.' : 'Update failed.',
        ], $updated ? 200 : 500);
    }
    //---------------
    public function delete(Request $request, int $id)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $companyId = (int) $user->company_id;

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
