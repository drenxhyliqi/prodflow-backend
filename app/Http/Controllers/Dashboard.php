<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $today     = now();
        $thisMonth = $today->month;
        $thisYear  = $today->year;
        $lastMonth = $today->copy()->subMonth();

        // ── STATS CARDS ──────────────────────────────────────────────

        // Revenue
        $revenueThis  = (float) \App\Models\SalesModel::where('company_id', $companyId)
            ->whereMonth('date', $thisMonth)->whereYear('date', $thisYear)
            ->selectRaw('SUM(qty * price) as total')->value('total');

        $revenueLast  = (float) \App\Models\SalesModel::where('company_id', $companyId)
            ->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)
            ->selectRaw('SUM(qty * price) as total')->value('total');

        $revenueGrowth = $revenueLast > 0
            ? round((($revenueThis - $revenueLast) / $revenueLast) * 100, 1)
            : null;

        $revenueToday = (float) \App\Models\SalesModel::where('company_id', $companyId)
            ->where('date', $today->toDateString())
            ->selectRaw('SUM(qty * price) as total')->value('total');

        // Transactions = rows count (each row = one product line item sold)
        $transactionsThis = \App\Models\SalesModel::where('company_id', $companyId)
            ->whereMonth('date', $thisMonth)->whereYear('date', $thisYear)->count();

        $transactionsLast = \App\Models\SalesModel::where('company_id', $companyId)
            ->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)->count();

        $transactionsGrowth = $transactionsLast > 0
            ? round((($transactionsThis - $transactionsLast) / $transactionsLast) * 100, 1)
            : null;

        $pendingVacationsCount = \App\Models\VacationsModel::where('company_id', $companyId)
            ->where('status', 'pending')->count();

        // Production efficiency = (actual / planned) * 100 for this month
        $actualProduction = (float) \App\Models\ProductionModel::where('company_id', $companyId)
            ->whereMonth('date', $thisMonth)->whereYear('date', $thisYear)->sum('qty');

        $plannedProduction = (float) \App\Models\PlanificationModel::where('company_id', $companyId)
            ->whereMonth('start_date', $thisMonth)->whereYear('start_date', $thisYear)->sum('planned_qty');

        $productionLastMonth = (float) \App\Models\ProductionModel::where('company_id', $companyId)
            ->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)->sum('qty');

        $productionGrowth = $productionLastMonth > 0
            ? round((($actualProduction - $productionLastMonth) / $productionLastMonth) * 100, 1)
            : null;

        $efficiencyRate = $plannedProduction > 0
            ? round(($actualProduction / $plannedProduction) * 100, 1)
            : null;

        // Staff
        $totalStaff = \App\Models\StaffModel::where('company_id', $companyId)->count();

        $onVacationToday = \App\Models\VacationsModel::where('company_id', $companyId)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today->toDateString())
            ->where('end_date', '>=', $today->toDateString())
            ->count();

        // ── CHARTS ───────────────────────────────────────────────────

        // Revenue vs Expenses — last 9 months
        $nineMonthsAgo = $today->copy()->subMonths(8)->startOfMonth();

        $revenueByMonth = \App\Models\SalesModel::where('company_id', $companyId)
            ->where('date', '>=', $nineMonthsAgo->toDateString())
            ->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(qty * price) as total')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date), MONTH(date)')
            ->get()->keyBy(fn($r) => "{$r->yr}-{$r->mo}");

        $expensesByMonth = \App\Models\ExpensesModel::where('company_id', $companyId)
            ->where('date', '>=', $nineMonthsAgo->toDateString())
            ->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(price) as total')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('YEAR(date), MONTH(date)')
            ->get()->keyBy(fn($r) => "{$r->yr}-{$r->mo}");

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

        // Weekly Production — last 7 days
        $sevenDaysAgo = $today->copy()->subDays(6)->startOfDay();

        $productionByDay = \App\Models\ProductionModel::where('company_id', $companyId)
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

        // Distribution chart — tries sales this month → production all-time → materials stock
        $salesByProduct = \App\Models\SalesModel::where('sales.company_id', $companyId)
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
            $productionByProduct = \App\Models\ProductionModel::where('production.company_id', $companyId)
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
                $chartItems = \App\Models\MaterialsStockModel::where('materials_stock.company_id', $companyId)
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

        $salesByCategory = $top3->map(fn($p) => [
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

        // ── TOP PRODUCTS ─────────────────────────────────────────────

        $topProducts = \App\Models\SalesModel::where('sales.company_id', $companyId)
            ->whereMonth('sales.date', $thisMonth)->whereYear('sales.date', $thisYear)
            ->join('products', 'sales.product_id', '=', 'products.pid')
            ->selectRaw('products.product, SUM(sales.qty) as total_qty, SUM(sales.qty * sales.price) as total_value')
            ->groupBy('sales.product_id', 'products.product')
            ->orderByDesc('total_value')
            ->limit(5)
            ->get()
            ->map(fn($p) => [
                'product'     => $p->product,
                'units_sold'  => (float) $p->total_qty,
                'total_value' => round((float) $p->total_value, 2),
            ]);

        // ── RECENT ACTIVITY ──────────────────────────────────────────

        // Recent sales joined with clients for client name
        $recentSales = \App\Models\SalesModel::where('sales.company_id', $companyId)
            ->leftJoin('clients', 'sales.client_id', '=', 'clients.cid')
            ->selectRaw('sales.sid, sales.qty, sales.price, sales.date, COALESCE(clients.client, "Unknown") as client_name')
            ->orderByDesc('sales.sid')
            ->limit(4)
            ->get()
            ->map(fn($s) => [
                'type'    => 'sale',
                'icon'    => 'cart',
                'title'   => "New sale — {$s->client_name}",
                'detail'  => '$' . number_format($s->qty * $s->price, 2),
                'date'    => $s->date,
                'sort_id' => $s->sid,
            ]);

        $recentProduction = \App\Models\ProductionModel::where('production.company_id', $companyId)
            ->join('products', 'production.product_id', '=', 'products.pid')
            ->selectRaw('production.pid as id, production.date, production.qty, products.product')
            ->orderByDesc('production.pid')
            ->limit(3)
            ->get()
            ->map(fn($p) => [
                'type'    => 'production',
                'icon'    => 'factory',
                'title'   => "Production batch — {$p->product}",
                'detail'  => number_format($p->qty, 0) . ' units',
                'date'    => $p->date,
                'sort_id' => $p->id,
            ]);

        $recentVacations = \App\Models\VacationsModel::where('vacations.company_id', $companyId)
            ->with('staff')
            ->orderByDesc('vid')
            ->limit(3)
            ->get()
            ->map(fn($v) => [
                'type'    => 'vacation',
                'icon'    => 'calendar',
                'title'   => "{$v->staff->name} {$v->staff->surname} requested leave",
                'detail'  => "{$v->start_date} — {$v->end_date}",
                'date'    => $v->start_date,
                'sort_id' => $v->vid,
            ]);

        $recentMaintenances = \App\Models\MaintenancesModel::where('maintenances.company_id', $companyId)
            ->with('machine')
            ->orderByDesc('mid')
            ->limit(3)
            ->get()
            ->map(fn($m) => [
                'type'    => 'maintenance',
                'icon'    => 'wrench',
                'title'   => "Maintenance — {$m->machine->machine}",
                'detail'  => $m->description,
                'date'    => $m->date,
                'sort_id' => $m->mid,
            ]);

        $recentActivity = $recentSales
            ->concat($recentProduction)
            ->concat($recentVacations)
            ->concat($recentMaintenances)
            ->sortByDesc('sort_id')
            ->take(10)
            ->values();

        // ── WAREHOUSE ────────────────────────────────────────────────

        $warehouseData = \App\Models\WarehousesModel::where('company_id', $companyId)
            ->selectRaw('COUNT(*) as active_warehouses, SUM(capacity) as max_stock_units')
            ->first();

        $activeWarehouses = (int) ($warehouseData->active_warehouses ?? 0);
        $rawMaxStock      = $warehouseData->max_stock_units;
        $maxStockUnits    = ($rawMaxStock !== null && (float) $rawMaxStock > 0)
                            ? round((float) $rawMaxStock, 2)
                            : null;

        $totalStockUnits = (float) \App\Models\MaterialsStockModel::where('company_id', $companyId)
            ->selectRaw('SUM(CASE WHEN type = "in" THEN qty ELSE -qty END) as net')
            ->value('net');
        $totalStockUnits = max(0, round($totalStockUnits, 2));

        $capacityPercent = ($maxStockUnits !== null && $maxStockUnits > 0)
            ? min(100, round(($totalStockUnits / $maxStockUnits) * 100, 1))
            : null;

        // ── TODAY'S SUMMARY ──────────────────────────────────────────

        $salesToday = \App\Models\SalesModel::where('company_id', $companyId)
            ->where('date', $today->toDateString())->count();

        $stockAlerts = \App\Models\MaterialsStockModel::where('materials_stock.company_id', $companyId)
            ->join('materials', 'materials_stock.material_id', '=', 'materials.mid')
            ->selectRaw('SUM(CASE WHEN materials_stock.type = "in" THEN materials_stock.qty ELSE -materials_stock.qty END) as current_stock')
            ->groupBy('materials_stock.material_id')
            ->havingRaw('current_stock <= 0')
            ->get()->count();

        $expiringContracts = \App\Models\ContractsModel::where('company_id', $companyId)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->where('end_date', '<=', $today->copy()->addDays(30)->toDateString())
            ->where('end_date', '>=', $today->toDateString())
            ->count();

        // ── RESPONSE ─────────────────────────────────────────────────

        return response()->json([
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
                    'this_month'       => round($actualProduction, 0),
                    'last_month'       => round($productionLastMonth, 0),
                    'growth_percent'   => $productionGrowth,
                    'efficiency_rate'  => $efficiencyRate,
                ],
                'staff' => [
                    'total'            => $totalStaff,
                    'on_vacation_today'=> $onVacationToday,
                ],
            ],
            'charts' => [
                'revenue_vs_expenses'       => $revenueVsExpenses,
                'weekly_production'         => $weeklyProduction,
                'distribution'              => $salesByCategory,
                'distribution_label'        => $chartLabel,
            ],
            'top_products'    => $topProducts,
            'recent_activity' => $recentActivity,
            'warehouse' => [
                'active_warehouses'  => $activeWarehouses,
                'total_stock_units'  => $totalStockUnits,
                'max_stock_units'    => $maxStockUnits,
                'capacity_percent'   => $capacityPercent,
            ],
            'today_summary' => [
                'sales_today'        => $salesToday,
                'pending_vacations'  => $pendingVacationsCount,
                'stock_alerts'       => $stockAlerts,
                'expiring_contracts' => $expiringContracts,
            ],
        ]);
    }
}
