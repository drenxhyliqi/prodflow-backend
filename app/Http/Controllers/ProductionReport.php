<?php

namespace App\Http\Controllers;

use App\Services\ProductionReportService;
use App\Services\ReportBatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductionReport extends Controller
{
    protected ProductionReportService $service;
    protected ReportBatchService $batchService;

    public function __construct(ProductionReportService $service, ReportBatchService $batchService)
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
        if ($cached = $this->batchService->sliceFromRun($request, 'production', 'summary')) {
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
        if ($cached = $this->batchService->sliceFromRun($request, 'production', 'trends')) {
            return response()->json($cached);
        }
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json(['success' => false, 'message' => 'Invalid date range.'], 422);
        }
        return response()->json($this->service->getTrends($request->user()->company_id, $dates['start_date'], $dates['end_date']));
    }

    public function machines(Request $request)
    {
        if ($cached = $this->batchService->sliceFromRun($request, 'production', 'machines')) {
            return response()->json($cached);
        }
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json(['success' => false, 'message' => 'Invalid date range.'], 422);
        }
        return response()->json($this->service->getMachinePerformance($request->user()->company_id, $dates['start_date'], $dates['end_date']));
    }

    public function topProducts(Request $request)
    {
        if ($cached = $this->batchService->sliceFromRun($request, 'production', 'top_products')) {
            return response()->json($cached);
        }
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json(['success' => false, 'message' => 'Invalid date range.'], 422);
        }
        return response()->json($this->service->getTopProducts($request->user()->company_id, $dates['start_date'], $dates['end_date']));
    }

    public function statusDistribution(Request $request)
    {
        if ($cached = $this->batchService->sliceFromRun($request, 'production', 'status_distribution')) {
            return response()->json($cached);
        }
        $dates = $this->validateDates($request);
        if (!$dates) {
            return response()->json(['success' => false, 'message' => 'Invalid date range.'], 422);
        }
        return response()->json($this->service->getStatusDistribution($request->user()->company_id, $dates['start_date'], $dates['end_date']));
    }
}
