<?php

namespace App\Http\Controllers;

use App\Services\MachinesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Machines extends Controller
{
    protected MachinesService $service;

    public function __construct(MachinesService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'machine'    => 'required|string|min:1|max:255',
            'type'       => 'required|string|min:1|max:255',
            'company_id' => 'required|numeric|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be filled in correctly.',
                'errors'  => $validator->errors()
            ], 422);
        }

        if ($this->service->createMachine($request->only(['machine', 'type', 'company_id']))) {
            return response()->json([
                'success' => true,
                'message' => 'The machine was successfully registered.'
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while saving the data. Please try again.'
        ], 500);
    }

    public function read()
    {
        return $this->service->getAllMachines(10);
    }

    public function edit($id)
    {
        $machine = $this->service->getMachineById($id);
        if (!$machine) {
            return response()->json([
                'success' => false,
                'message' => 'Machine not found.'
            ], 404);
        }
        return response()->json($machine);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mid'        => 'required|numeric|exists:machines,mid',
            'machine'    => 'required|string|min:1|max:255',
            'type'       => 'required|string|min:1|max:255',
            'company_id' => 'required|numeric|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $updated = $this->service->updateMachine(
            $request->input('mid'), 
            $request->only(['machine', 'type', 'company_id'])
        );
        
        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Machine updated successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Update failed'], 500);
    }

    public function delete($id)
    {
        if ($this->service->deleteMachine($id)) {
            return response()->json(['success' => true, 'message' => 'Machine deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Deletion failed.'], 500);
    }
}