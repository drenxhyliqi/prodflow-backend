<?php

namespace App\Http\Controllers;

use App\Services\MaintenancesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Maintenances extends Controller
{
    protected MaintenancesService $service;

    public function __construct(MaintenancesService $service)
    {
        $this->service = $service;
    }

    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'machine_id' => 'required|numeric|min:1',
            'date' => 'required|date',
            'description' => 'required|string|min:1|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $data = $request->only(['machine_id', 'date', 'description']);
            $companyId = $request->user()->company_id;
            
            if ($this->service->createMaintenance($data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Maintenance registered successfully.'
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
            $this->service->getAllMaintenances($companyId, 10, $search)
        );
    }

    //---------------
    public function edit(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        
        if (!$this->service->findOrFail($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance record not found.'
            ], 404);
        } else {
            return response()->json(
                $this->service->getById($id, $companyId)
            );
        }
    }

    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mid' => 'required|numeric|min:1',
            'machine_id' => 'required|numeric|min:1',
            'date' => 'required|date',
            'description' => 'required|string|min:1|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $id = $request->mid;
            $data = $request->only(['machine_id', 'date', 'description']);
            $companyId = $request->user()->company_id;
            
            if ($this->service->updateMaintenance($id, $data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'The maintenance was successfully updated.'
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
        
        if ($this->service->deleteMaintenance($id, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The maintenance was successfully deleted.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
}