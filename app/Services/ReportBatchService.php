<?php

namespace App\Services;

use App\Jobs\GenerateReportRunJob;
use App\Repositories\ReportBatchRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportBatchService
{
    public const TYPES = ['production', 'sales', 'expenses', 'materials_stock'];

    public function __construct(
        protected ReportBatchRepository $repository,
        protected SalesReportService $salesReportService,
        protected ProductionReportService $productionReportService,
        protected MaterialsStockService $materialsStockService,
    ) {}

    public function createBatch(int $companyId, ?int $userId, string $startDate, string $endDate, array $types): array
    {
        $types = array_values(array_intersect($types, self::TYPES));
        if (empty($types)) {
            throw new \InvalidArgumentException('No valid report types selected.');
        }

        return DB::transaction(function () use ($companyId, $userId, $startDate, $endDate, $types) {
            $batchId = $this->repository->createBatch([
                'company_id'      => $companyId,
                'user_id'         => $userId,
                'start_date'      => $startDate,
                'end_date'        => $endDate,
                'status'          => 'processing',
                'total_count'     => count($types),
                'completed_count' => 0,
                'failed_count'    => 0,
            ]);

            foreach ($types as $type) {
                $runId = $this->repository->createRun([
                    'batch_id'    => $batchId,
                    'report_type' => $type,
                    'status'      => 'queued',
                ]);
                GenerateReportRunJob::dispatch($runId);
            }

            return $this->formatBatch($this->repository->findBatch($batchId, $companyId));
        });
    }

    public function getBatch(int $batchId, int $companyId): ?array
    {
        $batch = $this->repository->findBatch($batchId, $companyId);
        return $batch ? $this->formatBatch($batch) : null;
    }

    public function getRunAccess(int $runId, int $companyId): ?array
    {
        $run = $this->repository->findRun($runId, $companyId);
        if (!$run) {
            return null;
        }

        return [
            'run_id'     => (int) $run->rid,
            'type'       => $run->report_type,
            'status'     => $run->status,
            'start_date' => (string) $run->start_date,
            'end_date'   => (string) $run->end_date,
        ];
    }

    /** Returns cached slice when run_id is present, otherwise null (use live query). */
    public function sliceFromRun(Request $request, string $type, string $slice)
    {
        $runId = $request->query('run_id');
        if (!$runId) {
            return null;
        }

        $run = $this->getRun((int) $runId, (int) $request->user()->company_id);
        if (!$run || $run['type'] !== $type || $run['status'] !== 'completed') {
            abort(409, 'Report is not ready yet.');
        }

        return $run['result'][$slice] ?? abort(404, 'Report slice not found.');
    }

    /** Full cached payload for expenses / materials_stock reports. */
    public function payloadFromRun(Request $request, string $type)
    {
        $runId = $request->query('run_id');
        if (!$runId) {
            return null;
        }

        $run = $this->getRun((int) $runId, (int) $request->user()->company_id);
        if (!$run || $run['type'] !== $type || $run['status'] !== 'completed') {
            abort(409, 'Report is not ready yet.');
        }

        return $run['result'];
    }

    public function generateRunData(int $runId): void
    {
        $run = DB::table('report_runs')
            ->join('report_batches', 'report_batches.bid', '=', 'report_runs.batch_id')
            ->where('report_runs.rid', $runId)
            ->select('report_runs.*', 'report_batches.company_id', 'report_batches.start_date', 'report_batches.end_date')
            ->first();

        if (!$run) {
            return;
        }

        $this->repository->updateRun($runId, ['status' => 'processing', 'started_at' => now()]);

        try {
            $result = $this->buildReportData($run->report_type, (int) $run->company_id, (string) $run->start_date, (string) $run->end_date);
            $this->repository->updateRun($runId, [
                'status' => 'completed', 'result' => json_encode($result),
                'error_message' => null, 'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $this->repository->updateRun($runId, [
                'status' => 'failed', 'error_message' => $e->getMessage(), 'completed_at' => now(),
            ]);
        }

        $this->syncBatchStatus((int) $run->batch_id);
    }

    private function getRun(int $runId, int $companyId): ?array
    {
        $run = $this->repository->findRun($runId, $companyId);
        if (!$run) {
            return null;
        }

        $data = [
            'run_id' => (int) $run->rid,
            'type'   => $run->report_type,
            'status' => $run->status,
            'result' => is_string($run->result) ? json_decode($run->result, true) : $run->result,
        ];

        return $data;
    }

    private function buildReportData(string $type, int $companyId, string $startDate, string $endDate): array
    {
        return match ($type) {
            'sales' => [
                'summary'         => $this->salesReportService->getSummary($companyId, $startDate, $endDate),
                'trends'          => $this->salesReportService->getTrends($companyId, $startDate, $endDate),
                'top_products'    => $this->salesReportService->getTopProducts($companyId, $startDate, $endDate),
                'top_clients'     => $this->salesReportService->getTopClients($companyId, $startDate, $endDate),
                'orders_overview' => $this->salesReportService->getOrdersOverview($companyId, $startDate, $endDate),
            ],
            'production' => [
                'summary'             => $this->productionReportService->getSummary($companyId, $startDate, $endDate),
                'trends'              => $this->productionReportService->getTrends($companyId, $startDate, $endDate),
                'machines'            => $this->productionReportService->getMachinePerformance($companyId, $startDate, $endDate),
                'top_products'        => $this->productionReportService->getTopProducts($companyId, $startDate, $endDate),
                'status_distribution' => $this->productionReportService->getStatusDistribution($companyId, $startDate, $endDate),
            ],
            'expenses' => [
                'success' => true,
                'total'   => (float) DB::table('expenses')->where('company_id', $companyId)
                    ->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate)->sum('price'),
                'data'    => DB::table('expenses')->where('company_id', $companyId)
                    ->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate)->orderBy('date')->get(),
            ],
            'materials_stock' => [
                'success' => true,
                'data'    => $this->materialsStockService->getStockReportData($companyId, $startDate, $endDate),
            ],
            default => throw new \InvalidArgumentException("Unknown report type: {$type}"),
        };
    }

    private function syncBatchStatus(int $batchId): void
    {
        $completed  = $this->repository->countRunsByStatus($batchId, 'completed');
        $failed     = $this->repository->countRunsByStatus($batchId, 'failed');
        $pending    = $this->repository->countRunsByStatus($batchId, 'queued')
                    + $this->repository->countRunsByStatus($batchId, 'processing');

        $status = 'processing';
        if ($pending === 0) {
            $status = $failed === 0 ? 'completed' : ($completed === 0 ? 'failed' : 'partial');
        }

        $this->repository->updateBatch($batchId, [
            'status' => $status, 'completed_count' => $completed, 'failed_count' => $failed,
        ]);
    }

    private function formatBatch(object $batch): array
    {
        $runs = $this->repository->getRunsByBatchId((int) $batch->bid);

        return [
            'batch_id'        => (int) $batch->bid,
            'status'          => $batch->status,
            'start_date'      => (string) $batch->start_date,
            'end_date'        => (string) $batch->end_date,
            'total_count'     => (int) $batch->total_count,
            'completed_count' => (int) $batch->completed_count,
            'failed_count'    => (int) $batch->failed_count,
            'runs'            => $runs->map(fn ($r) => [
                'run_id'        => (int) $r->rid,
                'type'          => $r->report_type,
                'status'        => $r->status,
                'error_message' => $r->error_message,
            ])->values()->all(),
        ];
    }
}
