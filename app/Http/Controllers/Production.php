<?php

namespace App\Http\Controllers;

use App\Services\ProductionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Production extends Controller
{
    protected ProductionService $service;
    public function __construct(ProductionService $service)
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
            'product_id' => 'required|integer|exists:products,pid',
            'machine_id' => 'required|integer|exists:machines,mid',
            'qty' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $request->only(['product_id', 'machine_id', 'qty', 'date']);
        $companyId = $user->company_id; 
        $productId = $payload['product_id'];
        $machineId = $payload['machine_id'];
        $qty = (float) $payload['qty'];

        if (! $this->service->checkProductBelongsToCompany($productId, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected product does not belong to this company.',
            ], 422);
        }

        if (! $this->service->checkMachineBelongsToCompany($machineId, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected machine does not belong to this company.',
            ], 422);
        }

        if ($this->service->hasDuplicateProduction(
            $productId,
            $machineId,
            $qty,
            $payload['date'],
            $companyId
        )) {
            return response()->json([
                'success' => false,
                'message' => 'This production record already exists with the same product, machine, quantity and date.',
            ], 409);
        }

        if ($this->service->createProduction($payload, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'Production registered successfully.',
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while saving production data. Please try again.',
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

        $companyId = $user->company_id;

        return $this->service->getAllProduction(10, $companyId);
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

        $companyId = $user->company_id;

        if (! $this->service->checkProductionExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Production record not found.',
            ], 404);
        }

        return $this->service->getProductionById($id, $companyId);
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
            'pid' => 'required|integer|exists:production,pid',
            'product_id' => 'required|integer|exists:products,pid',
            'machine_id' => 'required|integer|exists:machines,mid',
            'qty' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = $user->company_id;
        $pid = $request->input('pid');
        $productId = $request->input('product_id');
        $machineId = $request->input('machine_id');
        $qty = (float) $request->input('qty');

        if (! $this->service->checkProductionExist($pid, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Production record not found for this company.',
            ], 404);
        }

        if (! $this->service->checkProductBelongsToCompany($productId, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected product does not belong to this company.',
            ], 422);
        }

        if (! $this->service->checkMachineBelongsToCompany($machineId, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected machine does not belong to this company.',
            ], 422);
        }

        $data = $request->only(['product_id', 'machine_id', 'qty', 'date']);

        if ($this->service->hasDuplicateProduction(
            $productId,
            $machineId,
            $qty,
            (string) $data['date'],
            $companyId,
            $pid
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Another production record with the same product, machine, quantity and date already exists.',
            ], 409);
        }

        $updated = $this->service->updateProduction($pid, $data, $companyId);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Production updated.' : 'Update failed.',
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

        $companyId = $user->company_id;

        if (! $this->service->checkProductionExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Production record not found.',
            ], 404);
        }

        $deleted = $this->service->deleteProduction($id, $companyId);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Production deleted.' : 'Delete failed.',
        ], $deleted ? 200 : 500);
    }
}
