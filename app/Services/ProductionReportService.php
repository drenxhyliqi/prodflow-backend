<?php

namespace App\Services;

use App\Repositories\ProductionReportRepository;
use Carbon\Carbon;

class ProductionReportService
{
    protected ProductionReportRepository $repository;

    public function __construct(ProductionReportRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getSummary(int $companyId, string $startDate, string $endDate): array
    {
        $totalProduced  = $this->repository->getTotalProduced($companyId, $startDate, $endDate);
        $totalPlanned   = $this->repository->getTotalPlanned($companyId, $startDate, $endDate);
        $activePlans    = $this->repository->getActivePlansCount($companyId, $startDate, $endDate);
        $maintenances   = $this->repository->getMaintenanceCount($companyId, $startDate, $endDate);
        $productionDays = $this->repository->getProductionDaysCount($companyId, $startDate, $endDate);

        $totalDays         = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $efficiency        = $totalPlanned > 0 ? round(($totalProduced / $totalPlanned) * 100, 1) : 0;
        $avgDailyOutput    = $productionDays > 0 ? round($totalProduced / $productionDays, 1) : 0;

        return [
            'total_units_produced'  => $totalProduced,
            'production_efficiency' => $efficiency,
            'active_production_plans' => $activePlans,
            'downtime_incidents'    => $maintenances,
            'avg_daily_output'      => $avgDailyOutput,
            'production_days'       => $productionDays,
            'total_days_in_range'   => $totalDays,
        ];
    }
    //---------------
    public function getTrends(int $companyId, string $startDate, string $endDate): array
    {
        return [
            'daily'   => $this->repository->getDailyTrend($companyId, $startDate, $endDate),
            'weekly'  => $this->repository->getWeeklyTrend($companyId, $startDate, $endDate),
            'monthly' => $this->repository->getMonthlyTrend($companyId, $startDate, $endDate),
        ];
    }
    //---------------
    public function getMachinePerformance(int $companyId, string $startDate, string $endDate): array
    {
        $machines   = $this->repository->getMachinePerformance($companyId, $startDate, $endDate);
        $totalDays  = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $maxOutput  = collect($machines)->max('total_output') ?: 1;

        return collect($machines)->map(function ($m) use ($totalDays, $maxOutput) {
            $productionDays    = (int) $m->production_days;
            $maintenanceCount  = (int) $m->maintenance_count;
            $availableDays     = max(1, $totalDays - $maintenanceCount);
            $efficiency        = round(min(100, ($productionDays / $availableDays) * 100), 1);

            return [
                'machine'          => $m->machine,
                'type'             => $m->type,
                'total_output'     => (float) $m->total_output,
                'production_days'  => $productionDays,
                'maintenance_count' => $maintenanceCount,
                'efficiency'       => $efficiency,
            ];
        })->toArray();
    }
    //---------------
    public function getTopProducts(int $companyId, string $startDate, string $endDate): array
    {
        $products   = $this->repository->getTopProducts($companyId, $startDate, $endDate);
        $totalUnits = collect($products)->sum('units') ?: 1;

        return collect($products)->map(fn($p) => [
            'product_name' => $p->product_name,
            'unit'         => $p->unit,
            'units'        => (float) $p->units,
            'percentage'   => round(($p->units / $totalUnits) * 100, 1),
        ])->toArray();
    }
    //---------------
    public function getStatusDistribution(int $companyId, string $startDate, string $endDate): array
    {
        $distribution = $this->repository->getStatusDistribution($companyId, $startDate, $endDate);
        $total        = array_sum($distribution) ?: 1;

        return [
            'data' => [
                ['status' => 'Completed',   'count' => $distribution['completed'],   'percentage' => round($distribution['completed']   / $total * 100, 1)],
                ['status' => 'In Progress', 'count' => $distribution['in_progress'], 'percentage' => round($distribution['in_progress'] / $total * 100, 1)],
                ['status' => 'Pending',     'count' => $distribution['pending'],     'percentage' => round($distribution['pending']     / $total * 100, 1)],
                ['status' => 'Delayed',     'count' => $distribution['delayed'],     'percentage' => round($distribution['delayed']     / $total * 100, 1)],
            ],
            'total' => array_sum($distribution),
        ];
    }
}
