<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ReportBatchRepository
{
    public function createBatch(array $data): int
    {
        return (int) DB::table('report_batches')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    public function createRun(array $data): int
    {
        return (int) DB::table('report_runs')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    public function findBatch(int $batchId, int $companyId)
    {
        return DB::table('report_batches')
            ->where('bid', $batchId)
            ->where('company_id', $companyId)
            ->first();
    }

    public function getRunsByBatchId(int $batchId)
    {
        return DB::table('report_runs')
            ->where('batch_id', $batchId)
            ->orderBy('rid')
            ->get();
    }

    public function findRun(int $runId, int $companyId)
    {
        return DB::table('report_runs')
            ->join('report_batches', 'report_batches.bid', '=', 'report_runs.batch_id')
            ->where('report_runs.rid', $runId)
            ->where('report_batches.company_id', $companyId)
            ->select(
                'report_runs.*',
                'report_batches.start_date',
                'report_batches.end_date',
                'report_batches.company_id'
            )
            ->first();
    }

    public function updateRun(int $runId, array $data): bool
    {
        $data['updated_at'] = now();

        return DB::table('report_runs')->where('rid', $runId)->update($data) > 0;
    }

    public function updateBatch(int $batchId, array $data): bool
    {
        $data['updated_at'] = now();

        return DB::table('report_batches')->where('bid', $batchId)->update($data) > 0;
    }

    public function countRunsByStatus(int $batchId, string $status): int
    {
        return (int) DB::table('report_runs')
            ->where('batch_id', $batchId)
            ->where('status', $status)
            ->count();
    }
}
