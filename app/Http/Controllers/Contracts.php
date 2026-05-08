<?php

namespace App\Http\Controllers;

use App\Services\ContractsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Contracts extends Controller
{
    protected ContractsService $service;
    public function __construct(ContractsService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|numeric|exists:staff,sid',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $data = $request->only(['employee_id', 'start_date', 'end_date', 'status']);
            $data['company_id'] = $request->user()->company_id;

            if($this->service->createContract($data)){
                return response()->json([
                    'success' => true,
                    'message' => 'Contract registered successfully.'
                ], 201);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while saving the data. Please try again.'
                ], 500);
            }
        }
    }

    public function read(Request $request)
    {
        $search = $request->query('search', '');
        return response()->json(
            $this->service->getContracts(10, $search)
        );
    }

    public function readAll()
    {
        return response()->json(
            $this->service->getAllContracts()
        );
    }

    public function edit(int $id)
    {
        if(!$this->service->findOrFail($id)){
            return response()->json([
                'success' => false,
                'message' => 'Contract not found.'
            ], 404);
        }else{
            return $this->service->getContractById($id);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cid' => 'required|numeric|min:1|exists:contracts,cid',
            'employee_id' => 'required|numeric|exists:staff,sid',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'required|string|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $data = $request->only(['employee_id', 'start_date', 'end_date', 'status']);
            if ($this->service->updateContract($request->input('cid'), $data)) {
                return response()->json([
                    'success' => true,
                    'message' => 'The contract was successfully updated.'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No data was updated.'
                ], 500);
            }
        }
    }
    
    public function delete(int $id)
    {
        if ($this->service->deleteContract($id)) {
            return response()->json([
                'success' => true,
                'message' => 'The contract was successfully deleted.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
}