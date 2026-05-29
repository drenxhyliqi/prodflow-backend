<?php

namespace App\Http\Controllers;

use App\Services\ReportBatchService;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesReport extends Controller
{
    protected SalesReportService $service;
    protected ReportBatchService $batchService;

    public function __construct(SalesReportService $service, ReportBatchService $batchService)
    {
        $this->service = $service;
        $this->batchService = $batchService;
    }

    private function validateDates(Request $request): array|false
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date'   => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
        ]);
        if ($validator->fails()) {
            return false;
        }
        return ['start_date' => $request->query('start_date'), 'end_date' => $request->query('end_date')];
    }

    public function summary(Request $request)
    {
        if ($cached = $this->batchService->sliceFromRun($request, 'sales', 'summary')) {
            return response()->json($cached);
        }
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json(['success' => false, 'message' => 'Invalid date range.'], 422);
        }
        return response()->json($this->service->getSummary($request->user()->company_id, $dates['start_date'], $dates['end_date']));
    }

    public function trends(Request $request)
    {
        if ($cached = $this->batchService->sliceFromRun($request, 'sales', 'trends')) {
            return response()->json($cached);
        }
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json(['success' => false, 'message' => 'Invalid date range.'], 422);
        }
        return response()->json($this->service->getTrends($request->user()->company_id, $dates['start_date'], $dates['end_date']));
    }

    public function topProducts(Request $request)
    {
        if ($cached = $this->batchService->sliceFromRun($request, 'sales', 'top_products')) {
            return response()->json($cached);
        }
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json(['success' => false, 'message' => 'Invalid date range.'], 422);
        }
        return response()->json($this->service->getTopProducts($request->user()->company_id, $dates['start_date'], $dates['end_date']));
    }

    public function topClients(Request $request)
    {
        if ($cached = $this->batchService->sliceFromRun($request, 'sales', 'top_clients')) {
            return response()->json($cached);
        }
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json(['success' => false, 'message' => 'Invalid date range.'], 422);
        }
        return response()->json($this->service->getTopClients($request->user()->company_id, $dates['start_date'], $dates['end_date']));
    }

    public function ordersOverview(Request $request)
    {
        if ($cached = $this->batchService->sliceFromRun($request, 'sales', 'orders_overview')) {
            return response()->json($cached);
        }
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json(['success' => false, 'message' => 'Invalid date range.'], 422);
        }
        return response()->json($this->service->getOrdersOverview($request->user()->company_id, $dates['start_date'], $dates['end_date']));
    }
}
