<?php

namespace App\Http\Controllers;

use App\Services\StaffService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Staff extends Controller
{
    public function __construct(
        protected StaffService $service
    ) {}

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
            'name' => 'required|string|min:1|max:255',
            'surname' => 'required|string|min:1|max:255',
            'position' => 'required|string|min:1|max:255',
            'contact' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = $request->only(['name', 'surname', 'position', 'contact']);
        $payload['company_id'] = (int) $user->company_id;
        if (($payload['contact'] ?? null) === '') {
            $payload['contact'] = null;
        }

        if ($this->service->createStaff($payload)) {
            return response()->json([
                'success' => true,
                'message' => 'Staff registered successfully.',
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while saving the staff data. Please try again.',
        ], 500);
    }

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

        return $this->service->getAllStaff(10, $companyId);
    }

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

        if (! $this->service->checkStaffExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found.',
            ], 404);
        }

        return $this->service->getStaffById($id, $companyId);
    }

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
            'sid' => 'required|integer|exists:staff,sid',
            'name' => 'required|string|min:1|max:255',
            'surname' => 'required|string|min:1|max:255',
            'position' => 'required|string|min:1|max:255',
            'contact' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $companyId = (int) $user->company_id;
        $sid = (int) $request->input('sid');

        if (! $this->service->checkStaffExist($sid, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found for this company.',
            ], 404);
        }

        $data = $request->only(['name', 'surname', 'position', 'contact']);
        if (($data['contact'] ?? null) === '') {
            $data['contact'] = null;
        }

        $updated = $this->service->updateStaff($sid, $data, $companyId);

        return response()->json([
            'success' => $updated,
            'message' => $updated ? 'Staff updated.' : 'Update failed.',
        ], $updated ? 200 : 500);
    }

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

        if (! $this->service->checkStaffExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found.',
            ], 404);
        }

        return response()->json([
            'success' => $this->service->deleteStaff($id, $companyId),
        ]);
    }
}
