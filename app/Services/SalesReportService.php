<?php

namespace App\Services;

use App\Repositories\SalesReportRepository;
use Carbon\Carbon;

class SalesReportService
{
    protected SalesReportRepository $repository;

    public function __construct(SalesReportRepository $repository)
    {
        $this->repository = $repository;
    }
    //---------------
    public function getSummary(int $companyId, string $startDate, string $endDate): array
    {
        $totalRevenue  = $this->repository->getTotalRevenue($companyId, $startDate, $endDate);
        $totalOrders   = $this->repository->getTotalOrders($companyId, $startDate, $endDate);
        $totalExpenses = $this->repository->getTotalExpenses($companyId, $startDate, $endDate);
        $bestSeller    = $this->repository->getBestSeller($companyId, $startDate, $endDate);

        // Previous period of same length
        $start   = Carbon::parse($startDate);
        $end     = Carbon::parse($endDate);
        $days    = $start->diffInDays($end) + 1;
        $prevEnd = $start->copy()->subDay()->format('Y-m-d');
        $prevStart = Carbon::parse($prevEnd)->subDays($days - 1)->format('Y-m-d');

        $prevRevenue = $this->repository->getTotalRevenue($companyId, $prevStart, $prevEnd);
        $prevOrders  = $this->repository->getTotalOrders($companyId, $prevStart, $prevEnd);

        $revenueGrowth = $prevRevenue > 0 ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;
        $ordersGrowth  = $prevOrders  > 0 ? round((($totalOrders  - $prevOrders)  / $prevOrders)  * 100, 1) : 0;
        $avgOrderValue = $totalOrders  > 0 ? round($totalRevenue / $totalOrders, 2) : 0;
        $netProfit     = $totalRevenue - $totalExpenses;
        $profitMargin  = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        return [
            'total_revenue'   => $totalRevenue,
            'total_orders'    => $totalOrders,
            'avg_order_value' => $avgOrderValue,
            'total_expenses'  => $totalExpenses,
            'net_profit'      => $netProfit,
            'profit_margin'   => $profitMargin,
            'revenue_growth'  => $revenueGrowth,
            'orders_growth'   => $ordersGrowth,
            'best_seller'     => $bestSeller ? [
                'product_name' => $bestSeller->product_name,
                'units_sold'   => (float) $bestSeller->units_sold,
            ] : null,
        ];
    }
    //---------------
    public function getTrends(int $companyId, string $startDate, string $endDate): array
    {
        // Weekly — merge revenue + expenses by week_start key
        $weeklyRevenue  = collect($this->repository->getWeeklyRevenue($companyId, $startDate, $endDate))->keyBy('week_start');
        $weeklyExpenses = collect($this->repository->getWeeklyExpenses($companyId, $startDate, $endDate))->keyBy('week_start');
        $allWeeks       = $weeklyRevenue->keys()->merge($weeklyExpenses->keys())->unique()->sort()->values();

        $weekly = $allWeeks->map(function ($ws) use ($weeklyRevenue, $weeklyExpenses) {
            $r = $weeklyRevenue->get($ws);
            $e = $weeklyExpenses->get($ws);
            return [
                'week'       => $r['week'] ?? $e['week'],
                'week_start' => $ws,
                'revenue'    => $r['revenue']  ?? 0,
                'expenses'   => $e['expenses'] ?? 0,
            ];
        })->values()->toArray();

        // Monthly — merge revenue + expenses, then add MoM growth
        $monthlyRevenue  = collect($this->repository->getMonthlyRevenue($companyId, $startDate, $endDate))->keyBy('month');
        $monthlyExpenses = collect($this->repository->getMonthlyExpenses($companyId, $startDate, $endDate))->keyBy('month');
        $allMonths       = $monthlyRevenue->keys()->merge($monthlyExpenses->keys())->unique()->sort()->values();

        $merged = $allMonths->map(function ($month) use ($monthlyRevenue, $monthlyExpenses) {
            $r = $monthlyRevenue->get($month);
            $e = $monthlyExpenses->get($month);
            return [
                'month'    => $month,
                'revenue'  => $r['revenue']  ?? 0,
                'orders'   => $r['orders']   ?? 0,
                'expenses' => $e['expenses'] ?? 0,
            ];
        })->values()->toArray();

        $monthly = collect($merged)->map(function ($item, $index) use ($merged) {
            $prevRevenue = $index > 0 ? $merged[$index - 1]['revenue'] : 0;
            $growth = $prevRevenue > 0 ? round((($item['revenue'] - $prevRevenue) / $prevRevenue) * 100, 1) : 0;
            return array_merge($item, ['revenue_growth' => $growth]);
        })->toArray();

        return [
            'daily'   => $this->repository->getDailyRevenue($companyId, $startDate, $endDate),
            'weekly'  => $weekly,
            'monthly' => $monthly,
        ];
    }
    //---------------
    public function getTopProducts(int $companyId, string $startDate, string $endDate): array
    {
        $products     = $this->repository->getTopProducts($companyId, $startDate, $endDate);
        $totalRevenue = collect($products)->sum('revenue') ?: 1;

        return collect($products)->map(fn($p) => [
            'product_name' => $p->product_name,
            'unit'         => $p->unit,
            'units_sold'   => (float) $p->units_sold,
            'revenue'      => (float) $p->revenue,
            'percentage'   => round(($p->revenue / $totalRevenue) * 100, 1),
        ])->toArray();
    }
    //---------------
    public function getTopClients(int $companyId, string $startDate, string $endDate): array
    {
        $clients      = $this->repository->getTopClients($companyId, $startDate, $endDate);
        $totalRevenue = collect($clients)->sum('revenue') ?: 1;

        return collect($clients)->map(fn($c) => [
            'client'     => $c->client,
            'revenue'    => (float) $c->revenue,
            'orders'     => (int) $c->orders,
            'percentage' => round(($c->revenue / $totalRevenue) * 100, 1),
        ])->toArray();
    }
    //---------------
    public function getOrdersOverview(int $companyId, string $startDate, string $endDate): array
    {
        $rows = $this->repository->getMonthlyOrdersGrowth($companyId, $startDate, $endDate);

        return collect($rows)->map(function ($r, $index) use ($rows) {
            $prevOrders = $index > 0 ? (int) $rows[$index - 1]->orders : 0;
            $growth = $prevOrders > 0 ? round((((int) $r->orders - $prevOrders) / $prevOrders) * 100, 1) : 0;
            return [
                'month'   => date('M Y', mktime(0, 0, 0, $r->mo, 1, $r->yr)),
                'orders'  => (int) $r->orders,
                'revenue' => (float) $r->revenue,
                'growth'  => $growth,
            ];
        })->toArray();
    }
}
