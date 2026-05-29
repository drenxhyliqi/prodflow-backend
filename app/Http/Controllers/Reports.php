<?php

namespace App\Http\Controllers;

use App\Services\ReportBatchService;
use App\Services\ReportsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Reports extends Controller
{
    public function __construct(
        protected ReportsService $service,
        protected ReportBatchService $batchService,
    ) {}

    public function productsStock(Request $request)
    {
        $companyId = $request->user()->company_id;
        $search = $request->query('search', '');
        return response()->json($this->service->getProductsStock(10, $companyId, $search));
    }

    public function startBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date'   => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
            'types'      => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid batch input.', 'errors' => $validator->errors()], 422);
        }

        try {
            return response()->json($this->batchService->createBatch(
                (int) $request->user()->company_id,
                (int) $request->user()->uid,
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('types', [])
            ), 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function batchStatus(Request $request, int $id)
    {
        $batch = $this->batchService->getBatch($id, (int) $request->user()->company_id);
        if (!$batch) {
            return response()->json(['success' => false, 'message' => 'Batch not found.'], 404);
        }
        return response()->json($batch);
    }

    public function runAccess(Request $request, int $id)
    {
        $run = $this->batchService->getRunAccess($id, (int) $request->user()->company_id);
        if (!$run) {
            return response()->json(['success' => false, 'message' => 'Report run not found.'], 404);
        }
        if ($run['status'] !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Report is not ready yet.', 'status' => $run['status']], 409);
        }
        return response()->json($run);
    }
}
