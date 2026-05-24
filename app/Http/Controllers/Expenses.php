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
            'price' => 'required|numeric|min:1',
            'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $data = $request->only(['comment', 'price', 'date']);
            $companyId = $request->user()->company_id;
            if ($this->service->createExpense($data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Expense registered successfully.'
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
            $this->service->getAllExpenses($companyId, 10, $search)
        );
    }
    //---------------
    public function edit(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        if (!$this->service->findOrFail($id, $companyId)) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found.'
            ], 404);
        } else {
            return $this->service->getExpenseById($id, $companyId);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eid' => 'required|numeric|min:1|exists:expenses,eid',
            'comment' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:1',
            'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors'  => $validator->errors()
            ], 422);
        } else {
            $id = $request->eid;
            $data = $request->only(['comment', 'price', 'date']);
            $companyId = $request->user()->company_id;
            if ($this->service->updateExpense($id, $data, $companyId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'The expense was successfully updated.'
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
        if ($this->service->deleteExpense($id, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The expense was successfully deleted.'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong, please try again!'
            ], 500);
        }
    }
    //---------------
    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Please select valid dates.',
            'errors' => $validator->errors()
        ], 422);
        }

        $companyId = $request->user()->company_id;

        $expenses = \DB::table('expenses')
            ->where('company_id', $companyId)
            ->whereDate('date', '>=', $request->start_date)
            ->whereDate('date', '<=', $request->end_date)
            ->orderBy('date', 'ASC')
            ->get();

        $total = $expenses->sum('price');

        return response()->json([
            'success' => true,
            'total' => $total,
            'data' => $expenses
        ]);
    }
}
