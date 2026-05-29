<?php

namespace App\Services;

use App\Jobs\RefreshCompanyAnalyticsJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AnalyticsCacheService
{
    public const TTL_MINUTES = 30;

    public const STATUS_IDLE = 'idle';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public static function dashboardKey(int $companyId): string
    {
        return "analytics:dashboard:{$companyId}";
    }

    public static function alertsKey(int $companyId): string
    {
        return "analytics:alerts:{$companyId}";
    }

    public static function refreshStatusKey(int $companyId): string
    {
        return "analytics:refresh_status:{$companyId}";
    }

    public static function salesReportKey(int $companyId, string $type, string $startDate, string $endDate): string
    {
        return "analytics:sales_report:{$companyId}:{$type}:{$startDate}:{$endDate}";
    }

    public static function productionReportKey(int $companyId, string $type, string $startDate, string $endDate): string
    {
        return "analytics:production_report:{$companyId}:{$type}:{$startDate}:{$endDate}";
    }

    public function __construct(
        protected DashboardService $dashboardService,
        protected AlertsService $alertsService,
    ) {}

    public function getDashboard(int $companyId): array
    {
        $key = self::dashboardKey($companyId);

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $data = $this->dashboardService->build($companyId);
        Cache::put($key, $data, now()->addMinutes(self::TTL_MINUTES));

        return $data;
    }

    public function getAlerts(int $companyId): array
    {
        $key = self::alertsKey($companyId);

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $data = $this->alertsService->build($companyId);
        Cache::put($key, $data, now()->addMinutes(self::TTL_MINUTES));

        return $data;
    }

    public function getRefreshStatus(int $companyId): array
    {
        $status = Cache::get(self::refreshStatusKey($companyId));

        if (!$status) {
            return $this->idleStatus();
        }

        if (
            ($status['status'] ?? self::STATUS_IDLE) === self::STATUS_COMPLETED
            && !empty($status['completed_at'])
            && now()->diffInSeconds(Carbon::parse($status['completed_at'])) > 15
        ) {
            Cache::forget(self::refreshStatusKey($companyId));

            return $this->idleStatus();
        }

        return $status;
    }

    public function markRefreshing(int $companyId): void
    {
        Cache::put(self::refreshStatusKey($companyId), [
            'refreshing'    => true,
            'status'        => self::STATUS_PROCESSING,
            'message'       => 'Updating dashboard, alerts, and reports in the background...',
            'current_step'  => 'starting',
            'steps'         => ['dashboard', 'alerts', 'reports'],
            'started_at'    => now()->toIso8601String(),
            'completed_at'  => null,
        ], now()->addMinutes(5));
    }

    public function updateRefreshProgress(int $companyId, string $step): void
    {
        $status = Cache::get(self::refreshStatusKey($companyId), $this->idleStatus());

        $messages = [
            'dashboard' => 'Updating dashboard...',
            'alerts'    => 'Updating alerts...',
            'reports'   => 'Updating reports...',
        ];

        Cache::put(self::refreshStatusKey($companyId), array_merge($status, [
            'refreshing'   => true,
            'status'       => self::STATUS_PROCESSING,
            'current_step' => $step,
            'message'      => $messages[$step] ?? $status['message'],
        ]), now()->addMinutes(5));
    }

    public function markRefreshComplete(int $companyId): void
    {
        $existing = Cache::get(self::refreshStatusKey($companyId), []);

        Cache::put(self::refreshStatusKey($companyId), [
            'refreshing'   => false,
            'status'       => self::STATUS_COMPLETED,
            'message'      => 'Background update completed successfully.',
            'current_step' => null,
            'steps'        => ['dashboard', 'alerts', 'reports'],
            'started_at'   => $existing['started_at'] ?? now()->toIso8601String(),
            'completed_at' => now()->toIso8601String(),
        ], now()->addMinutes(5));
    }

    public function markRefreshFailed(int $companyId, string $error): void
    {
        Cache::put(self::refreshStatusKey($companyId), [
            'refreshing'   => false,
            'status'       => self::STATUS_FAILED,
            'message'      => 'Background update failed. Please try again.',
            'error'        => $error,
            'current_step' => null,
            'completed_at' => now()->toIso8601String(),
        ], now()->addMinutes(5));
    }

    public function refreshDashboard(int $companyId): void
    {
        Cache::put(
            self::dashboardKey($companyId),
            $this->dashboardService->build($companyId),
            now()->addMinutes(self::TTL_MINUTES)
        );
    }

    public function refreshAlerts(int $companyId): void
    {
        Cache::put(
            self::alertsKey($companyId),
            $this->alertsService->build($companyId),
            now()->addMinutes(self::TTL_MINUTES)
        );
    }

    public function refreshReports(int $companyId): void
    {
        $salesReportService = app(SalesReportService::class);
        $productionReportService = app(ProductionReportService::class);

        $start = now()->startOfMonth()->format('Y-m-d');
        $end   = now()->endOfMonth()->format('Y-m-d');

        $salesReportService->getSummary($companyId, $start, $end);
        $salesReportService->getTrends($companyId, $start, $end);
        $salesReportService->getTopProducts($companyId, $start, $end);
        $salesReportService->getTopClients($companyId, $start, $end);
        $salesReportService->getOrdersOverview($companyId, $start, $end);

        $productionReportService->getSummary($companyId, $start, $end);
        $productionReportService->getTrends($companyId, $start, $end);
        $productionReportService->getMachinePerformance($companyId, $start, $end);
        $productionReportService->getTopProducts($companyId, $start, $end);
        $productionReportService->getStatusDistribution($companyId, $start, $end);
    }

    public function runFullRefresh(int $companyId): void
    {
        try {
            $this->updateRefreshProgress($companyId, 'dashboard');
            $this->refreshDashboard($companyId);

            $this->updateRefreshProgress($companyId, 'alerts');
            $this->refreshAlerts($companyId);

            $this->updateRefreshProgress($companyId, 'reports');
            $this->refreshReports($companyId);

            $this->markRefreshComplete($companyId);
        } catch (\Throwable $e) {
            $this->markRefreshFailed($companyId, $e->getMessage());
            throw $e;
        }
    }

    public static function dispatchRefresh(int $companyId): array
    {
        $instance = app(self::class);
        $instance->markRefreshing($companyId);
        RefreshCompanyAnalyticsJob::dispatch($companyId);

        return $instance->getRefreshStatus($companyId);
    }

    public static function withBackgroundRefresh(array $payload, int $companyId): array
    {
        return array_merge($payload, [
            'background_refresh' => app(self::class)->getRefreshStatus($companyId),
        ]);
    }

    private function idleStatus(): array
    {
        return [
            'refreshing'   => false,
            'status'       => self::STATUS_IDLE,
            'message'      => null,
            'current_step' => null,
            'completed_at' => null,
        ];
    }
}
