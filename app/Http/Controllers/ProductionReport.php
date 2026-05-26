<?php

namespace App\Http\Controllers;

use App\Services\ProductionReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProductionReport extends Controller
{
    protected ProductionReportService $service;

    public function __construct(ProductionReportService $service)
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

        $companyId = $request->user()->company_id;

        return response()->json(
            $this->service->getSummary($companyId, $dates['start_date'], $dates['end_date'])
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

        $companyId = $request->user()->company_id;

        return response()->json(
            $this->service->getTrends($companyId, $dates['start_date'], $dates['end_date'])
        );
    }
    //---------------
    public function machines(Request $request)
    {
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date range. Use format: YYYY-MM-DD and ensure start_date <= end_date.'
            ], 422);
        }

        $companyId = $request->user()->company_id;

        return response()->json(
            $this->service->getMachinePerformance($companyId, $dates['start_date'], $dates['end_date'])
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

        $companyId = $request->user()->company_id;

        return response()->json(
            $this->service->getTopProducts($companyId, $dates['start_date'], $dates['end_date'])
        );
    }
    //---------------
    public function statusDistribution(Request $request)
    {
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date range. Use format: YYYY-MM-DD and ensure start_date <= end_date.'
            ], 422);
        }

        $companyId = $request->user()->company_id;

        return response()->json(
            $this->service->getStatusDistribution($companyId, $dates['start_date'], $dates['end_date'])
        );
    }
}
