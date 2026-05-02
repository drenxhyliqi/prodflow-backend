<?php

namespace App\Http\Controllers;

use App\Services\MaterialsStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialsStock extends Controller
{
    protected MaterialsStockService $service;
    public function __construct(MaterialsStockService $service)
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
            'material_id' => 'required|integer|exists:materials,mid',
            'type' => 'required|string|in:IN,OUT',
            'qty' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'warehouse_id' => 'required|integer|exists:warehouses,wid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = $user->company_id;
        $materialId = $request->input('material_id');
        $warehouseId = $request->input('warehouse_id');

        if (! $this->service->checkMaterialBelongsToCompany($materialId, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected material does not belong to this company.',
            ], 422);
        }

        if (! $this->service->checkWarehouseBelongsToCompany($warehouseId, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected warehouse does not belong to this company.',
            ], 422);
        }

        $payload = $request->only(['material_id', 'type', 'qty', 'date', 'warehouse_id']);
        $payload['type'] = strtoupper((string) $payload['type']);
        $companyId = $user->company_id;

        $created = $this->service->createMaterialsStock($payload, $companyId);

        return response()->json([
            'success' => $created,
            'message' => $created ? 'Material stock transaction registered.' : 'Create failed.',
        ], $created ? 201 : 500);
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

        return $this->service->getAllMaterialsStock(10, $companyId);
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

        if (! $this->service->checkMaterialsStockExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
            ], 404);
        }

        return $this->service->getMaterialsStockById($id, $companyId);
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
            'msid' => 'required|integer|exists:materials_stock,msid',
            'material_id' => 'required|integer|exists:materials,mid',
            'type' => 'required|string|in:IN,OUT',
            'qty' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'warehouse_id' => 'required|integer|exists:warehouses,wid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = $user->company_id;
        $msid = $request->input('msid');
        $materialId = $request->input('material_id');
        $warehouseId = $request->input('warehouse_id');

        if (! $this->service->checkMaterialsStockExist($msid, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found for this company.',
            ], 404);
        }

        if (! $this->service->checkMaterialBelongsToCompany($materialId, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected material does not belong to this company.',
            ], 422);
        }

        if (! $this->service->checkWarehouseBelongsToCompany($warehouseId, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected warehouse does not belong to this company.',
            ], 422);
        }

        $data = $request->only(['material_id', 'type', 'qty', 'date', 'warehouse_id']);
        $data['type'] = strtoupper((string) $data['type']);
        $updated = $this->service->updateMaterialsStock($msid, $data, $companyId);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Material stock transaction updated.' : 'Update failed.',
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

        if (! $this->service->checkMaterialsStockExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
            ], 404);
        }

        $deleted = $this->service->deleteMaterialsStock($id, $companyId);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Material stock transaction deleted.' : 'Delete failed.',
        ], $deleted ? 200 : 500);
    }
}
