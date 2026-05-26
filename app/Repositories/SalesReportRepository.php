<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class SalesReportRepository
{
    //---------------
    public function getTotalRevenue(int $companyId, string $startDate, string $endDate): float
    {
        return (float) DB::table('sales')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total');
    }
    //---------------
    public function getTotalOrders(int $companyId, string $startDate, string $endDate): int
    {
        return DB::table('sales')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->distinct()
            ->count('sale_number');
    }
    //---------------
    public function getTotalExpenses(int $companyId, string $startDate, string $endDate): float
    {
        return (float) DB::table('expenses')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('price');
    }
    //---------------
    public function getBestSeller(int $companyId, string $startDate, string $endDate): ?object
    {
        return DB::table('sales as s')
            ->join('products as p', 's.product_id', '=', 'p.pid')
            ->where('s.company_id', $companyId)
            ->whereBetween('s.date', [$startDate, $endDate])
            ->selectRaw('p.product as product_name, SUM(s.qty) as units_sold')
            ->groupBy('s.product_id', 'p.product')
            ->orderByDesc('units_sold')
            ->first();
    }
    //---------------
    public function getDailyRevenue(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('sales')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($r) => ['date' => $r->date, 'revenue' => (float) $r->revenue])
            ->toArray();
    }
    //---------------
    public function getWeeklyRevenue(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('sales')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('YEAR(date) as yr, WEEK(date, 1) as wk, MIN(date) as week_start, SUM(total) as revenue')
            ->groupByRaw('YEAR(date), WEEK(date, 1)')
            ->orderByRaw('YEAR(date), WEEK(date, 1)')
            ->get()
            ->map(fn($r) => [
                'week'       => 'Wk ' . $r->wk,
                'week_start' => $r->week_start,
                'revenue'    => (float) $r->revenue,
            ])
            ->toArray();
    }
    //---------------
    public function getWeeklyExpenses(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('expenses')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('YEAR(date) as yr, WEEK(date, 1) as wk, MIN(date) as week_start, SUM(price) as expenses')
            ->groupByRaw('YEAR(date), WEEK(date, 1)')
            ->orderByRaw('YEAR(date), WEEK(date, 1)')
            ->get()
            ->map(fn($r) => [
                'week'       => 'Wk ' . $r->wk,
                'week_start' => $r->week_start,
                'expenses'   => (float) $r->expenses,
            ])
            ->toArray();
    }
    //---------------
    public function getMonthlyRevenue(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('sales')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(total) as revenue, COUNT(DISTINCT sale_number) as orders')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date), MONTH(date)')
            ->get()
            ->map(fn($r) => [
                'month'   => date('M Y', mktime(0, 0, 0, $r->mo, 1, $r->yr)),
                'revenue' => (float) $r->revenue,
                'orders'  => (int) $r->orders,
            ])
            ->toArray();
    }
    //---------------
    public function getMonthlyExpenses(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('expenses')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(price) as expenses')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date), MONTH(date)')
            ->get()
            ->map(fn($r) => [
                'month'    => date('M Y', mktime(0, 0, 0, $r->mo, 1, $r->yr)),
                'expenses' => (float) $r->expenses,
            ])
            ->toArray();
    }
    //---------------
    public function getTopProducts(int $companyId, string $startDate, string $endDate, int $limit = 10): array
    {
        return DB::table('sales as s')
            ->join('products as p', 's.product_id', '=', 'p.pid')
            ->where('s.company_id', $companyId)
            ->whereBetween('s.date', [$startDate, $endDate])
            ->selectRaw('p.product as product_name, p.unit, SUM(s.qty) as units_sold, SUM(s.total) as revenue')
            ->groupBy('s.product_id', 'p.product', 'p.unit')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }
    //---------------
    public function getTopClients(int $companyId, string $startDate, string $endDate, int $limit = 10): array
    {
        return DB::table('sales')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('client')
            ->selectRaw('client, SUM(total) as revenue, COUNT(DISTINCT sale_number) as orders')
            ->groupBy('client')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }
    //---------------
    public function getMonthlyOrdersGrowth(int $companyId, string $startDate, string $endDate): array
    {
        return DB::table('sales')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('YEAR(date) as yr, MONTH(date) as mo, COUNT(DISTINCT sale_number) as orders, SUM(total) as revenue')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date), MONTH(date)')
            ->get()
            ->toArray();
    }
}
