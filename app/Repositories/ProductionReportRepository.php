<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ProductionReportRepository
{
    //---------------
    public function getTotalProduced(int $companyId, string $startDate, string $endDate): float
    {
        return (float) DB::table('production')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('qty');
    }
    //---------------
    public function getTotalPlanned(int $companyId, string $startDate, string $endDate): float
    {
        return (float) DB::table('planification')
            ->where('company_id', $companyId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->sum('planned_qty');
    }
    //---------------
    public function getActivePlansCount(int $companyId, string $startDate, string $endDate): int
    {
        return DB::table('planification')
            ->where('company_id', $companyId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->count();
    }
    //---------------
    public function getMaintenanceCount(int $companyId, string $startDate, string $endDate): int
    {
        return DB::table('maintenances')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();
    }
    //---------------
    public function getProductionDaysCount(int $companyId, string $startDate, string $endDate): int
    {
        return DB::table('production')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->distinct()
            ->count('date');
    }
    //---------------
    public function getDailyTrend(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('production')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(qty) as units')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($r) => ['date' => $r->date, 'units' => (float) $r->units])
            ->toArray();
    }
    //---------------
    public function getWeeklyTrend(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('production')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('YEAR(date) as yr, WEEK(date, 1) as wk, MIN(date) as week_start, SUM(qty) as units')
            ->groupByRaw('YEAR(date), WEEK(date, 1)')
            ->orderByRaw('YEAR(date), WEEK(date, 1)')
            ->get()
            ->map(fn($r) => [
                'week'       => 'Wk ' . $r->wk,
                'week_start' => $r->week_start,
                'units'      => (float) $r->units,
            ])
            ->toArray();
    }
    //---------------
    public function getMonthlyTrend(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('production')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(qty) as units')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date), MONTH(date)')
            ->get()
            ->map(fn($r) => [
                'month' => date('M Y', mktime(0, 0, 0, $r->mo, 1, $r->yr)),
                'units' => (float) $r->units,
            ])
            ->toArray();
    }
    //---------------
    public function getMachinePerformance(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('machines as m')
            ->where('m.company_id', $companyId)
            ->selectRaw("
                m.mid,
                m.machine,
                m.type,
                COALESCE(SUM(p.qty), 0) as total_output,
                COUNT(DISTINCT p.date) as production_days,
                COUNT(DISTINCT mt.mid) as maintenance_count
            ")
            ->leftJoin('production as p', function ($join) use ($startDate, $endDate) {
                $join->on('p.machine_id', '=', 'm.mid')
                     ->where('p.date', '>=', $startDate)
                     ->where('p.date', '<=', $endDate);
            })
            ->leftJoin('maintenances as mt', function ($join) use ($startDate, $endDate) {
                $join->on('mt.machine_id', '=', 'm.mid')
                     ->where('mt.date', '>=', $startDate)
                     ->where('mt.date', '<=', $endDate);
            })
            ->groupBy('m.mid', 'm.machine', 'm.type')
            ->orderByDesc('total_output')
            ->get()
            ->toArray();
    }
    //---------------
    public function getTopProducts(int $companyId, string $startDate, string $endDate, int $limit = 10): array
    {
        return DB::table('production as p')
            ->join('products as pr', 'p.product_id', '=', 'pr.pid')
            ->where('p.company_id', $companyId)
            ->whereBetween('p.date', [$startDate, $endDate])
            ->selectRaw('pr.product as product_name, pr.unit, SUM(p.qty) as units')
            ->groupBy('p.product_id', 'pr.product', 'pr.unit')
            ->orderByDesc('units')
            ->limit($limit)
            ->get()
            ->toArray();
    }
    //---------------
    public function getStatusDistribution(int $companyId, string $startDate, string $endDate): array
    {
        $rows = DB::table('planification')
            ->where('company_id', $companyId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->selectRaw("
                status,
                COUNT(*) as count,
                SUM(CASE WHEN status != 'completed' AND end_date < CURDATE() THEN 1 ELSE 0 END) as delayed_count
            ")
            ->groupBy('status')
            ->get();

        $delayedCount = DB::table('planification')
            ->where('company_id', $companyId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('end_date', '<', now()->toDateString())
            ->count();

        $distribution = [
            'completed'   => 0,
            'in_progress' => 0,
            'pending'     => 0,
            'delayed'     => $delayedCount,
        ];

        foreach ($rows as $row) {
            if (isset($distribution[$row->status])) {
                $distribution[$row->status] = (int) $row->count;
            }
        }

        // Subtract delayed from pending/in_progress to avoid double counting
        $distribution['pending']     = max(0, $distribution['pending'] - $delayedCount);
        $distribution['in_progress'] = max(0, $distribution['in_progress']);

        return $distribution;
    }
}
