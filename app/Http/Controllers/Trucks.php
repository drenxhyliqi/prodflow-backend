<?php

namespace App\Http\Controllers;

use App\Services\TrucksService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Trucks extends Controller
{
    protected TrucksService $service;
    public function __construct(TrucksService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'truck' => 'required|string|min:1|max:255',
            'license_plate' => 'required|string|min:1|max:255',
            'capacity' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $data = $request->only(['truck', 'license_plate', 'capacity']);
            $companyId = $request->user()->company_id;
            if ($this->service->createTruck($data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Truck registered successfully.'
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
            $this->service->getAllTrucks($companyId, 10, $search)
        );
    }
    //---------------
    public function edit(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if (!$this->service->findOrFail($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Truck not found.'
            ], 404);
        } else {
            return $this->service->getTruckById($id, $companyId);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tid' => 'required|numeric|min:1|exists:trucks,tid',
            'truck' => 'required|string|min:1|max:255',
            'license_plate' => 'required|string|min:1|max:255',
            'capacity' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $id = $request->tid;
            $data = $request->only(['truck', 'license_plate', 'capacity']);
            $companyId = $request->user()->company_id;
            if ($this->service->updateTruck($id, $data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'The truck was successfully updated.'
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
        if ($this->service->deleteTruck($id, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The truck was successfully deleted.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }

    //---------------
    public function changeStatus(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if ($this->service->changeTruckStatus($id, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The truck status was successfully changed.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
}
