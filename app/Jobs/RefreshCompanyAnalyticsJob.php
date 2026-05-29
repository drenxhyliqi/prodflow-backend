<?php

namespace App\Jobs;

use App\Services\AnalyticsCacheService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RefreshCompanyAnalyticsJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $uniqueFor = 120;

    public function __construct(public int $companyId) {}

    public function uniqueId(): string
    {
        return (string) $this->companyId;
    }

    public function handle(AnalyticsCacheService $cacheService): void
    {
        $cacheService->runFullRefresh($this->companyId);
    }

    public function failed(\Throwable $exception): void
    {
        app(AnalyticsCacheService::class)->markRefreshFailed(
            $this->companyId,
            $exception->getMessage()
        );
    }
}
