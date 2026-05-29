<?php

namespace App\Services;

use App\Models\ContractsModel;
use App\Models\ExpensesModel;
use App\Models\MaintenancesModel;
use App\Models\MaterialsStockModel;
use App\Models\PlanificationModel;
use App\Models\ProductionModel;
use App\Models\SalesModel;
use App\Models\StaffModel;
use App\Models\VacationsModel;
use App\Models\WarehousesModel;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function build(int $companyId): array
    {
        $today     = now();
        $thisMonth = $today->month;
        $thisYear  = $today->year;
        $lastMonth = $today->copy()->subMonth();

        $revenueThis = (float) SalesModel::where('company_id', $companyId)
            ->whereMonth('date', $thisMonth)->whereYear('date', $thisYear)
            ->selectRaw('SUM(qty * price) as total')->value('total');

        $revenueLast = (float) SalesModel::where('company_id', $companyId)
            ->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)
            ->selectRaw('SUM(qty * price) as total')->value('total');

        $revenueGrowth = $revenueLast > 0
            ? round((($revenueThis - $revenueLast) / $revenueLast) * 100, 1)
            : null;

        $revenueToday = (float) SalesModel::where('company_id', $companyId)
            ->where('date', $today->toDateString())
            ->selectRaw('SUM(qty * price) as total')->value('total');

        $transactionsThis = SalesModel::where('company_id', $companyId)
            ->whereMonth('date', $thisMonth)->whereYear('date', $thisYear)->count();

        $transactionsLast = SalesModel::where('company_id', $companyId)
            ->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)->count();

        $transactionsGrowth = $transactionsLast > 0
            ? round((($transactionsThis - $transactionsLast) / $transactionsLast) * 100, 1)
            : null;

        $pendingVacationsCount = VacationsModel::where('company_id', $companyId)
            ->where('status', 'pending')->count();

        $actualProduction = (float) ProductionModel::where('company_id', $companyId)
            ->whereMonth('date', $thisMonth)->whereYear('date', $thisYear)->sum('qty');

        $plannedProduction = (float) PlanificationModel::where('company_id', $companyId)
            ->whereMonth('start_date', $thisMonth)->whereYear('start_date', $thisYear)->sum('planned_qty');

        $productionLastMonth = (float) ProductionModel::where('company_id', $companyId)
            ->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)->sum('qty');

        $productionGrowth = $productionLastMonth > 0
            ? round((($actualProduction - $productionLastMonth) / $productionLastMonth) * 100, 1)
            : null;

        $efficiencyRate = $plannedProduction > 0
            ? round(($actualProduction / $plannedProduction) * 100, 1)
            : null;

        $totalStaff = StaffModel::where('company_id', $companyId)->count();

        $onVacationToday = VacationsModel::where('company_id', $companyId)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today->toDateString())
            ->where('end_date', '>=', $today->toDateString())
            ->count();

        $nineMonthsAgo = $today->copy()->subMonths(8)->startOfMonth();

        $revenueByMonth = SalesModel::where('company_id', $companyId)
            ->where('date', '>=', $nineMonthsAgo->toDateString())
            ->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(qty * price) as total')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date), MONTH(date)')
            ->get()->keyBy(fn ($r) => "{$r->yr}-{$r->mo}");

        $expensesByMonth = ExpensesModel::where('company_id', $companyId)
            ->where('date', '>=', $nineMonthsAgo->toDateString())
            ->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(price) as total')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date), MONTH(date)')
            ->get()->keyBy(fn ($r) => "{$r->yr}-{$r->mo}");

        $revenueVsExpenses = [];
        for ($i = 8; $i >= 0; $i--) {
            $m   = $today->copy()->subMonths($i);
            $key = "{$m->year}-{$m->month}";
            $revenueVsExpenses[] = [
                'month'    => $m->format('M Y'),
                'revenue'  => (float) ($revenueByMonth[$key]->total ?? 0),
                'expenses' => (float) ($expensesByMonth[$key]->total ?? 0),
            ];
        }

        $sevenDaysAgo = $today->copy()->subDays(6)->startOfDay();

        $productionByDay = ProductionModel::where('company_id', $companyId)
            ->where('date', '>=', $sevenDaysAgo->toDateString())
            ->selectRaw('date, SUM(qty) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()->keyBy('date');

        $weeklyProduction = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $today->copy()->subDays($i)->toDateString();
            $weeklyProduction[] = [
                'day'   => date('D', strtotime($d)),
                'date'  => $d,
                'units' => (float) ($productionByDay[$d]->total ?? 0),
            ];
        }

        $salesByProduct = SalesModel::where('sales.company_id', $companyId)
            ->whereMonth('sales.date', $thisMonth)->whereYear('sales.date', $thisYear)
            ->join('products', 'sales.product_id', '=', 'products.pid')
            ->selectRaw('products.product as name, SUM(sales.qty * sales.price) as value')
            ->groupBy('sales.product_id', 'products.product')
            ->orderByDesc('value')
            ->get();

        if ($salesByProduct->isNotEmpty()) {
            $chartLabel = 'Sales by Product';
            $chartItems = $salesByProduct;
        } else {
            $productionByProduct = ProductionModel::where('production.company_id', $companyId)
                ->join('products', 'production.product_id', '=', 'products.pid')
                ->selectRaw('products.product as name, SUM(production.qty) as value')
                ->groupBy('production.product_id', 'products.product')
                ->orderByDesc('value')
                ->get();

            if ($productionByProduct->isNotEmpty()) {
                $chartLabel = 'Production by Product';
                $chartItems = $productionByProduct;
            } else {
                $chartLabel = 'Materials in Stock';
                $chartItems = MaterialsStockModel::where('materials_stock.company_id', $companyId)
                    ->join('materials', 'materials_stock.material_id', '=', 'materials.mid')
                    ->selectRaw('materials.material as name, SUM(CASE WHEN materials_stock.type = "in" THEN materials_stock.qty ELSE -materials_stock.qty END) as value')
                    ->groupBy('materials_stock.material_id', 'materials.material')
                    ->havingRaw('value > 0')
                    ->orderByDesc('value')
                    ->get();
            }
        }

        $grandTotal = $chartItems->sum('value');
        $top3       = $chartItems->take(3);
        $otherTotal = $chartItems->skip(3)->sum('value');

        $salesByCategory = $top3->map(fn ($p) => [
            'name'    => $p->name,
            'value'   => round((float) $p->value, 2),
            'percent' => $grandTotal > 0 ? round(($p->value / $grandTotal) * 100) : 0,
        ])->values()->toArray();

        if ($otherTotal > 0) {
            $salesByCategory[] = [
                'name'    => 'Other',
                'value'   => round((float) $otherTotal, 2),
                'percent' => $grandTotal > 0 ? round(($otherTotal / $grandTotal) * 100) : 0,
            ];
        }

        $topProducts = SalesModel::where('sales.company_id', $companyId)
            ->whereMonth('sales.date', $thisMonth)->whereYear('sales.date', $thisYear)
            ->join('products', 'sales.product_id', '=', 'products.pid')
            ->selectRaw('products.product, SUM(sales.qty) as total_qty, SUM(sales.qty * sales.price) as total_value')
            ->groupBy('sales.product_id', 'products.product')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'product'     => $p->product,
                'units_sold'  => (float) $p->total_qty,
                'total_value' => round((float) $p->total_value, 2),
            ]);

        $todayStr = $today->toDateString();

        $clearThresholds = Cache::get("activity_clear_{$companyId}", [
            'sid' => 0, 'pid' => 0, 'vid' => 0, 'mid' => 0,
        ]);

        $recentSales = SalesModel::where('sales.company_id', $companyId)
            ->where('sales.sid', '>', $clearThresholds['sid'])
            ->selectRaw('sales.sid, sales.qty, sales.price, sales.date, COALESCE(sales.client, "Unknown") as client_name')
            ->orderByDesc('sales.sid')
            ->limit(4)
            ->get()
            ->map(fn ($s) => [
                'type'     => 'sale',
                'icon'     => 'cart',
                'title'    => "New sale — {$s->client_name}",
                'detail'   => '$' . number_format($s->qty * $s->price, 2),
                'date'     => $s->date,
                'sort_key' => $s->date . str_pad((string) $s->sid, 12, '0', STR_PAD_LEFT),
            ]);

        $recentProduction = ProductionModel::where('production.company_id', $companyId)
            ->where('production.pid', '>', $clearThresholds['pid'])
            ->join('products', 'production.product_id', '=', 'products.pid')
            ->selectRaw('production.pid as id, production.date, production.qty, products.product')
            ->orderByDesc('production.pid')
            ->limit(3)
            ->get()
            ->map(fn ($p) => [
                'type'     => 'production',
                'icon'     => 'factory',
                'title'    => "Production batch — {$p->product}",
                'detail'   => number_format($p->qty, 0) . ' units',
                'date'     => $p->date,
                'sort_key' => $p->date . str_pad((string) $p->id, 12, '0', STR_PAD_LEFT),
            ]);

        $recentVacations = VacationsModel::where('vacations.company_id', $companyId)
            ->where('vid', '>', $clearThresholds['vid'])
            ->with('staff')
            ->orderByDesc('vid')
            ->limit(3)
            ->get()
            ->map(fn ($v) => [
                'type'     => 'vacation',
                'icon'     => 'calendar',
                'title'    => "{$v->staff->name} {$v->staff->surname} requested leave",
                'detail'   => "{$v->start_date} — {$v->end_date}",
                'date'     => $v->start_date,
                'sort_key' => min($v->start_date, $todayStr) . str_pad((string) $v->vid, 12, '0', STR_PAD_LEFT),
            ]);

        $recentMaintenances = MaintenancesModel::where('maintenances.company_id', $companyId)
            ->where('mid', '>', $clearThresholds['mid'])
            ->with('machine')
            ->orderByDesc('mid')
            ->limit(3)
            ->get()
            ->map(fn ($m) => [
                'type'     => 'maintenance',
                'icon'     => 'wrench',
                'title'    => "Maintenance — {$m->machine->machine}",
                'detail'   => $m->description,
                'date'     => $m->date,
                'sort_key' => $m->date . str_pad((string) $m->mid, 12, '0', STR_PAD_LEFT),
            ]);

        $recentActivity = $recentSales
            ->concat($recentProduction)
            ->concat($recentVacations)
            ->concat($recentMaintenances)
            ->sortByDesc('sort_key')
            ->take(10)
            ->values();

        $warehouseData = WarehousesModel::where('company_id', $companyId)
            ->selectRaw('COUNT(*) as active_warehouses, SUM(capacity) as max_stock_units')
            ->first();

        $activeWarehouses = (int) ($warehouseData->active_warehouses ?? 0);
        $rawMaxStock      = $warehouseData->max_stock_units;
        $maxStockUnits    = ($rawMaxStock !== null && (float) $rawMaxStock > 0)
            ? round((float) $rawMaxStock, 2)
            : null;

        $totalStockUnits = (float) MaterialsStockModel::where('company_id', $companyId)
            ->selectRaw('SUM(CASE WHEN type = "in" THEN qty ELSE -qty END) as net')
            ->value('net');
        $totalStockUnits = max(0, round($totalStockUnits, 2));

        $capacityPercent = ($maxStockUnits !== null && $maxStockUnits > 0)
            ? min(100, round(($totalStockUnits / $maxStockUnits) * 100, 1))
            : null;

        $salesToday = SalesModel::where('company_id', $companyId)
            ->where('date', $today->toDateString())->count();

        $stockAlerts = MaterialsStockModel::where('materials_stock.company_id', $companyId)
            ->join('materials', 'materials_stock.material_id', '=', 'materials.mid')
            ->selectRaw('SUM(CASE WHEN materials_stock.type = "in" THEN materials_stock.qty ELSE -materials_stock.qty END) as current_stock')
            ->groupBy('materials_stock.material_id')
            ->havingRaw('current_stock <= 0')
            ->get()->count();

        $expiringContracts = ContractsModel::where('company_id', $companyId)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->where('end_date', '<=', $today->copy()->addDays(30)->toDateString())
            ->where('end_date', '>=', $today->toDateString())
            ->count();

        $recentActivity->transform(function ($item) {
            unset($item['sort_key']);
            return $item;
        });

        return [
            'stats' => [
                'revenue' => [
                    'this_month'     => round($revenueThis, 2),
                    'last_month'     => round($revenueLast, 2),
                    'growth_percent' => $revenueGrowth,
                    'today'          => round($revenueToday, 2),
                ],
                'transactions' => [
                    'this_month'     => $transactionsThis,
                    'last_month'     => $transactionsLast,
                    'growth_percent' => $transactionsGrowth,
                    'pending'        => $pendingVacationsCount,
                ],
                'production' => [
                    'this_month'      => round($actualProduction, 0),
                    'last_month'      => round($productionLastMonth, 0),
                    'growth_percent'  => $productionGrowth,
                    'efficiency_rate' => $efficiencyRate,
                ],
                'staff' => [
                    'total'             => $totalStaff,
                    'on_vacation_today' => $onVacationToday,
                ],
            ],
            'charts' => [
                'revenue_vs_expenses' => $revenueVsExpenses,
                'weekly_production'   => $weeklyProduction,
                'distribution'        => $salesByCategory,
                'distribution_label'  => $chartLabel,
            ],
            'top_products'    => $topProducts,
            'recent_activity' => $recentActivity,
            'warehouse' => [
                'active_warehouses' => $activeWarehouses,
                'total_stock_units' => $totalStockUnits,
                'max_stock_units'   => $maxStockUnits,
                'capacity_percent'  => $capacityPercent,
            ],
            'today_summary' => [
                'sales_today'        => $salesToday,
                'pending_vacations'  => $pendingVacationsCount,
                'stock_alerts'       => $stockAlerts,
                'expiring_contracts' => $expiringContracts,
            ],
        ];
    }
}
