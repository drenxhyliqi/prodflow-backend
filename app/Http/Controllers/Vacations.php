<?php

namespace App\Http\Controllers;

use App\Services\VacationsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Vacations extends Controller
{
    protected VacationsService $service;

    public function __construct(VacationsService $service)
    {
        $this->service = $service;
    }

    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['staff_id', 'start_date', 'end_date', 'reason', 'status']);
        $companyId = $request->user()->company_id;

        if ($this->service->create($data, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'Vacation registered successfully.'
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while saving the data. Please try again.'
        ], 500);
    }

    //---------------
    public function read(Request $request)
    {
        $search = $request->query('search', '');
        $companyId = $request->user()->company_id;

        return response()->json(
            $this->service->getVacations($companyId, 10, $search)
        );
    }

    //---------------
    public function edit(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;
        $vacation = $this->service->findVacation($id, $companyId);

        if (!$vacation) {
            return response()->json([
                'success' => false,
                'message' => 'Vacation record not found.'
            ], 404);
        }

        return response()->json($vacation);
    }

    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vid' => 'required|numeric|min:1',
            'staff_id' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'All fields must be completed according to the rules.',
                'errors' => $validator->errors()
            ], 422);
        }

        $id = $request->vid;
        $data = $request->only(['staff_id', 'start_date', 'end_date', 'reason', 'status']);
        $companyId = $request->user()->company_id;

        if ($this->service->update($id, $data, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The vacation was successfully updated.'
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'No data was updated.'
        ], 500);
    }

    //---------------
    public function delete(Request $request, int $id)
    {
        $companyId = $request->user()->company_id;

        if ($this->service->delete($id, $companyId)) {
            return response()->json([
                'success' => true,
                'message' => 'The vacation was successfully deleted.'
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong, please try again!'
        ], 500);
    }
}
