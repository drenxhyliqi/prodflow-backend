<?php

namespace App\Http\Controllers;

use App\Services\SalariesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Salaries extends Controller
{
    protected SalariesService $service;
    public function __construct(SalariesService $service)
    {
        $this->service = $service;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:staff,sid',
            'salary' => 'required|numeric|min:1',
            'comment' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $data = $request->only(['employee_id', 'salary', 'comment']);
            $companyId = $request->user()->company_id;
            if ($this->service->createSalary($data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Salary registered successfully.'
                ], 201);
            } else {
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
        $companyId = $request->user()->company_id;
        return response()->json(
            $this->service->getSalaries($companyId, 10, $search)
        );
    }

    public function readAll(Request $request)
    {
        $companyId = $request->user()->company_id;
        return response()->json(
            $this->service->getAllSalaries($companyId)
        );
    }

    public function edit(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if (!$this->service->findOrFail($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Salary record not found.'
            ], 404);
        } else {
            return response()->json($this->service->getSalaryById($id, $companyId));
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sid' => 'required|numeric|min:1|exists:salaries,sid',
            'employee_id' => 'required|integer|exists:staff,sid',
            'salary' => 'required|numeric|min:1',
            'comment' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $id = $request->sid;
            $data = $request->only(['employee_id', 'salary', 'comment']);
            $companyId = $request->user()->company_id;
            if ($this->service->updateSalary($id, $data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'The salary was successfully updated.'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No data was updated.'
                ], 500);
            }
        }
    }

    public function delete(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if ($this->service->deleteSalary($id, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The salary record was successfully deleted.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
}