<?php

namespace App\Http\Controllers;

use App\Services\PlanificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Planification extends Controller
{
    protected PlanificationService $service;

    public function __construct(PlanificationService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,pid',
            'planned_qty' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $data = $request->only(['product_id', 'planned_qty', 'start_date', 'end_date', 'status']);
            $companyId = $request->user()->company_id;
            if ($this->service->createPlanification($data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Planification registered successfully.'
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
        $companyId = $request->user()->company_id;
        return response()->json(
            $this->service->getAllPlanification($companyId, 10)
        );
    }
    //---------------
    public function edit(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if (!$this->service->checkPlanificationExist($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Planification not found.'
            ], 404);
        } else {
            return $this->service->getPlanificationById($id, $companyId);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pid' => 'required|integer|exists:planification,pid',
            'product_id' => 'required|integer|exists:products,pid',
            'planned_qty' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $id = $request->pid;
            $data = $request->only(['product_id', 'planned_qty', 'start_date', 'end_date', 'status']);
            $companyId = $request->user()->company_id;
            if ($this->service->updatePlanification($id, $data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Planification updated successfully.'
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
        if ($this->service->deletePlanification($id, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'Planification deleted successfully.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
}
