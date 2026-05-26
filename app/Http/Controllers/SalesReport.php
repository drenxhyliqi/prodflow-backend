<?php

namespace App\Http\Controllers;

use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesReport extends Controller
{
    protected SalesReportService $service;

    public function __construct(SalesReportService $service)
    {
        $this->service = $service;
    }
    //---------------
    private function validateDates(Request $request): array|false
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date'   => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return false;
        }

        return [
            'start_date' => $request->query('start_date'),
            'end_date'   => $request->query('end_date'),
        ];
    }
    //---------------
    public function summary(Request $request)
    {
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date range. Use format: YYYY-MM-DD and ensure start_date <= end_date.'
            ], 422);
        }

        return response()->json(
            $this->service->getSummary($request->user()->company_id, $dates['start_date'], $dates['end_date'])
        );
    }
    //---------------
    public function trends(Request $request)
    {
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date range. Use format: YYYY-MM-DD and ensure start_date <= end_date.'
            ], 422);
        }

        return response()->json(
            $this->service->getTrends($request->user()->company_id, $dates['start_date'], $dates['end_date'])
        );
    }
    //---------------
    public function topProducts(Request $request)
    {
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date range. Use format: YYYY-MM-DD and ensure start_date <= end_date.'
            ], 422);
        }

        return response()->json(
            $this->service->getTopProducts($request->user()->company_id, $dates['start_date'], $dates['end_date'])
        );
    }
    //---------------
    public function topClients(Request $request)
    {
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date range. Use format: YYYY-MM-DD and ensure start_date <= end_date.'
            ], 422);
        }

        return response()->json(
            $this->service->getTopClients($request->user()->company_id, $dates['start_date'], $dates['end_date'])
        );
    }
    //---------------
    public function ordersOverview(Request $request)
    {
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date range. Use format: YYYY-MM-DD and ensure start_date <= end_date.'
            ], 422);
        }

        return response()->json(
            $this->service->getOrdersOverview($request->user()->company_id, $dates['start_date'], $dates['end_date'])
        );
    }
}
