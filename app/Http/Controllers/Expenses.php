<?php

namespace App\Http\Controllers;

use App\Services\ExpensesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class Expenses extends Controller
{
    protected ExpensesService $service;
    public function __construct(ExpensesService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'company_id' => 'required|numeric|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            if ($this->service->createExpense($request->only(['comment', 'price', 'date', 'company_id']))) {
                return response()->json([
                    'success' => true,
                    'message' => 'Expense created successfully.'
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
    public function read()
    {
        return $this->service->getAllExpenses(10);
    }
    //---------------
    public function edit($id)
    {
        if (!$this->service->findOrFail($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found.'
            ], 404);
        } else {
            return $this->service->getExpenseById($id);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eid' => 'required|numeric|min:1|exists:expenses,eid',
            'comment' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'company_id' => 'required|numeric|exists:companies,cid',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'All fields must be filled in according to the rules.');
        } else {
            return $this->service->updateExpense($request->input('eid'), $request->only(['comment', 'price', 'date', 'company_id']));
        }
    }
    //---------------
    public function delete($id)
    {
        return $this->service->deleteExpense($id);
    }
}
