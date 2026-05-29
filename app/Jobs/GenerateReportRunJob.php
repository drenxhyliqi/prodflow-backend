<?php

namespace App\Jobs;

use App\Services\ReportBatchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateReportRunJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public int $runId) {}

    public function handle(ReportBatchService $service): void
    {
        $service->generateRunData($this->runId);
    }
}
