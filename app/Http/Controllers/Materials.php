<?php

namespace App\Http\Controllers;

use App\Services\MaterialsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Materials extends Controller
{
    protected MaterialsService $service;
    public function __construct(MaterialsService $service)
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
            'material' => 'required|string|min:1|max:255',
            'unit' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $request->only(['material', 'unit']);
        $companyId = $user->company_id;

        if ($this->service->hasDuplicateMaterial(
            $payload['material'],
            $payload['unit'],
            $companyId
        )) {
            return response()->json([
                'success' => false,
                'message' => 'This material already exists.',
            ], 409);
        }

        if ($this->service->createMaterial($payload, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'Material registered successfully.',
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while saving the material data. Please try again.',
        ], 500);
    }
    //---------------
    public function read(Request $request)
    {
        $search = $request->query('search', '');
        $companyId = $request->user()->company_id;
        return $this->service->getAllMaterials(10, $companyId, $search);
    }
    //---------------
    public function edit(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if (! $this->service->checkMaterialExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Material not found.',
            ], 404);
        }

        return $this->service->getMaterialById($id, $companyId);
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
            'mid' => 'required|integer|exists:materials,mid',
            'material' => 'required|string|min:1|max:255',
            'unit' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = (int) $user->company_id;
        $mid = (int) $request->input('mid');

        if (! $this->service->checkMaterialExist($mid, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Material not found for this company.',
            ], 404);
        }

        $data = $request->only(['material', 'unit']);

        if ($this->service->hasDuplicateMaterial(
            $data['material'],
            $data['unit'],
            $companyId,
            $mid
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Another material with the same details already exists.',
            ], 409);
        }

        $updated = $this->service->updateMaterial($mid, $data, $companyId);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Material updated.' : 'Update failed.',
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

        if (! $this->service->checkMaterialExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Material not found.',
            ], 404);
        }

        $deleted = $this->service->deleteMaterial($id, $companyId);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'Material deleted.' : 'Delete failed.',
        ], $deleted ? 200 : 500);
    }
}
