<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function chat(Request $request)
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'user', 'content' => $request->message],
                        ],
                        'max_tokens' => 100,
                    ]);

            return response()->json([
                'reply' => $response->json('choices.0.message.content'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function analyzeText(Request $request)
    {
        try {
            $request->validate(['text' => 'required|string']);

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Analyze this vacation reason text. 
                                      Reply ONLY in raw JSON, no markdown, no backticks.
                                      Format: {"sentiment":"positive","topics":["topic1"],"summary":"one line"}'
                            ],
                            ['role' => 'user', 'content' => $request->text],
                        ],
                        'max_tokens' => 200,
                    ]);

            return response()->json([
                'analysis' => json_decode(
                    $response->json('choices.0.message.content'),
                    true
                ),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function chatWithData(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'company_id' => 'required|integer',
            ]);

            $systemPrompt = $this->buildSystemPrompt(
                $request->message,
                $request->company_id
            );

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $request->message],
                        ],
                        'max_tokens' => 1200,
                    ]);

            return response()->json([
                'reply' => $response->json('choices.0.message.content'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function detectSections(string $message): array
    {
        $msg = mb_strtolower($message);

        $allTriggers = [
            'analizë',
            'analiz',
            'analysis',
            'overview',
            'raport',
            'report',
            'gjithçka',
            'everything',
            'plotë',
            'full',
            'gjithë',
            'komplet',
            'all',
            'summary'
        ];
        foreach ($allTriggers as $kw) {
            if (str_contains($msg, $kw)) {
                return [
                    'staff',
                    'vacations',
                    'contracts',
                    'sales',
                    'expenses',
                    'financial',
                    'production',
                    'materials',
                    'machines',
                    'suppliers',
                    'products'
                ];
            }
        }

        $map = [
            'staff' => ['staff', 'punonjës', 'punonjësi', 'punonjës', 'punëtor', 'employee', 'ekip', 'team'],
            'vacations' => ['pushim', 'vacation', 'leave', 'mungesë', 'absence', 'off', 'pushime'],
            'contracts' => ['kontratë', 'kontrata', 'contract', 'skadoj', 'skadon', 'expire', 'expiring'],
            'sales' => ['shitje', 'sale', 'revenue', 'të ardhura', 'ardhura', 'klient', 'client', 'transaksion', 'transaction', 'shes'],
            'expenses' => ['shpenzim', 'shpenzime', 'expense', 'cost', 'kosto', 'harxhim'],
            'financial' => ['fitim', 'profit', 'financ', 'margin', 'humbje', 'loss', 'bilanci', 'balance'],
            'production' => ['prodhim', 'production', 'njësi', 'unit', 'prodhoj', 'prodhuar'],
            'materials' => ['material', 'stok', 'stock', 'lëndë', 'inventar', 'inventory', 'magazinë'],
            'machines' => ['makineri', 'makinë', 'machine', 'servis', 'maintenance', 'mirëmbajtje'],
            'suppliers' => ['furnizues', 'supplier', 'furnitor'],
            'products' => ['produkt', 'produkti', 'product', 'çmim', 'price'],
        ];

        $sections = [];
        foreach ($map as $section => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($msg, $kw)) {
                    $sections[] = $section;
                    break;
                }
            }
        }

        // financial kërkon sales + expenses
        if (in_array('financial', $sections)) {
            $sections[] = 'sales';
            $sections[] = 'expenses';
        }

        // default nëse asgjë nuk u detektua
        if (empty($sections)) {
            $sections = ['staff', 'sales', 'expenses', 'financial'];
        }

        return array_unique($sections);
    }

    private function buildSystemPrompt(string $message, int $companyId): string
    {
        $today = now();
        $thisMonth = $today->month;
        $thisYear = $today->year;
        $lastMonth = $today->copy()->subMonth();
        $sections = $this->detectSections($message);
        $has = fn(string $s) => in_array($s, $sections);

        // ── QUERIES — ngarko vetëm seksionet e nevojshme ─────────────

        $staff = $has('staff') ? \App\Models\StaffModel::where('company_id', $companyId)->get(['name', 'surname', 'position', 'contact']) : collect();
        $totalClients = $has('sales') ? \App\Models\ClientsModel::where('company_id', $companyId)->count() : 0;

        // Vacations
        $allVacations = $onLeaveToday = $pendingVacations = $upcomingVacations = collect();
        if ($has('vacations')) {
            $allVacations = \App\Models\VacationsModel::where('company_id', $companyId)->with('staff')->orderByDesc('start_date')->get();
            $onLeaveToday = $allVacations->filter(fn($v) => $v->status === 'approved' && $v->start_date <= $today->toDateString() && $v->end_date >= $today->toDateString());
            $pendingVacations = $allVacations->filter(fn($v) => $v->status === 'pending');
            $upcomingVacations = $allVacations->filter(fn($v) => $v->status === 'approved' && $v->start_date > $today->toDateString() && $v->start_date <= $today->copy()->addDays(7)->toDateString());
        }

        // Contracts
        $activeContracts = $expiredContracts = 0;
        $expiringContracts = collect();
        if ($has('contracts')) {
            $activeContracts = \App\Models\ContractsModel::where('company_id', $companyId)->whereRaw('LOWER(status) = ?', ['active'])->count();
            $expiredContracts = \App\Models\ContractsModel::where('company_id', $companyId)->whereRaw('LOWER(status) = ?', ['expired'])->count();
            $expiringContracts = \App\Models\ContractsModel::where('company_id', $companyId)->whereRaw('LOWER(status) = ?', ['active'])->where('end_date', '<=', $today->copy()->addDays(30)->toDateString())->where('end_date', '>=', $today->toDateString())->with('employee')->get();
        }

        // Sales
        $monthSales = $lastMonthSales = $todaySales = null;
        $salesTrend = $topClients = $topProducts = collect();
        if ($has('sales')) {
            $monthSales = \App\Models\SalesModel::where('company_id', $companyId)->whereMonth('date', $thisMonth)->whereYear('date', $thisYear)->selectRaw('SUM(qty * price) as total, COUNT(*) as count')->first();
            $lastMonthSales = \App\Models\SalesModel::where('company_id', $companyId)->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)->selectRaw('SUM(qty * price) as total, COUNT(*) as count')->first();
            $todaySales = \App\Models\SalesModel::where('company_id', $companyId)->where('date', $today->toDateString())->selectRaw('SUM(qty * price) as total, COUNT(*) as count')->first();
            $salesTrend = \App\Models\SalesModel::where('company_id', $companyId)->where('date', '>=', $today->copy()->subMonths(6)->startOfMonth()->toDateString())->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(qty * price) as total, COUNT(*) as count')->groupByRaw('YEAR(date), MONTH(date)')->orderByRaw('YEAR(date), MONTH(date)')->get();
            $topClients = \App\Models\SalesModel::where('sales.company_id', $companyId)->whereMonth('sales.date', $thisMonth)->join('clients', 'sales.client_id', '=', 'clients.cid')->selectRaw('clients.client, SUM(sales.qty * sales.price) as total, COUNT(*) as orders')->groupBy('clients.cid', 'clients.client')->orderByDesc('total')->limit(5)->get();
            $topProducts = \App\Models\SalesModel::where('sales.company_id', $companyId)->whereMonth('sales.date', $thisMonth)->whereYear('sales.date', $thisYear)->join('products', 'sales.product_id', '=', 'products.pid')->selectRaw('products.product, SUM(sales.qty) as total_qty, SUM(sales.qty * sales.price) as total_value')->groupBy('sales.product_id', 'products.product')->orderByDesc('total_value')->limit(5)->get();
        }

        // Expenses
        $monthExpenses = $lastMonthExpenses = null;
        $expensesTrend = $recentExpenses = collect();
        if ($has('expenses')) {
            $monthExpenses = \App\Models\ExpensesModel::where('company_id', $companyId)->whereMonth('date', $thisMonth)->whereYear('date', $thisYear)->selectRaw('SUM(price) as total, COUNT(*) as count')->first();
            $lastMonthExpenses = \App\Models\ExpensesModel::where('company_id', $companyId)->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)->selectRaw('SUM(price) as total')->first();
            $expensesTrend = \App\Models\ExpensesModel::where('company_id', $companyId)->where('date', '>=', $today->copy()->subMonths(6)->startOfMonth()->toDateString())->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(price) as total')->groupByRaw('YEAR(date), MONTH(date)')->orderByRaw('YEAR(date), MONTH(date)')->get();
            $recentExpenses = \App\Models\ExpensesModel::where('company_id', $companyId)->orderByDesc('date')->limit(5)->get(['comment', 'price', 'date']);
        }

        // Financial calculations (kërkon sales + expenses)
        $monthRevenue = (float) ($monthSales->total ?? 0);
        $lastMonthRevenue = (float) ($lastMonthSales->total ?? 0);
        $monthExpTotal = (float) ($monthExpenses->total ?? 0);
        $lastMonthExpTotal = (float) ($lastMonthExpenses->total ?? 0);
        $profit = $monthRevenue - $monthExpTotal;
        $lastMonthProfit = $lastMonthRevenue - $lastMonthExpTotal;
        $revenueGrowth = $lastMonthRevenue > 0 ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : null;
        $expenseGrowth = $lastMonthExpTotal > 0 ? round((($monthExpTotal - $lastMonthExpTotal) / $lastMonthExpTotal) * 100, 1) : null;
        $profitGrowth = $lastMonthProfit > 0 ? round((($profit - $lastMonthProfit) / abs($lastMonthProfit)) * 100, 1) : null;

        // Production
        $todayProduction = $monthProduction = $lastMonthProduction = $productionGrowth = 0;
        $productionTrend = $activePlans = collect();
        if ($has('production')) {
            $todayProduction = \App\Models\ProductionModel::where('company_id', $companyId)->where('date', $today->toDateString())->sum('qty');
            $monthProduction = \App\Models\ProductionModel::where('company_id', $companyId)->whereMonth('date', $thisMonth)->whereYear('date', $thisYear)->sum('qty');
            $lastMonthProduction = \App\Models\ProductionModel::where('company_id', $companyId)->whereMonth('date', $lastMonth->month)->whereYear('date', $lastMonth->year)->sum('qty');
            $productionTrend = \App\Models\ProductionModel::where('company_id', $companyId)->where('date', '>=', $today->copy()->subMonths(6)->startOfMonth()->toDateString())->selectRaw('YEAR(date) as yr, MONTH(date) as mo, SUM(qty) as total')->groupByRaw('YEAR(date), MONTH(date)')->orderByRaw('YEAR(date), MONTH(date)')->get();
            $productionGrowth = $lastMonthProduction > 0 ? round((($monthProduction - $lastMonthProduction) / $lastMonthProduction) * 100, 1) : null;
            $allPlans = \App\Models\PlanificationModel::where('company_id', $companyId)->orderByDesc('start_date')->get(['product_id', 'planned_qty', 'start_date', 'end_date', 'status']);
            $activePlans = $allPlans->whereIn('status', ['pending', 'in_progress']);
        }

        // Products
        $products = collect();
        if ($has('products')) {
            $products = \App\Models\ProductsModel::where('company_id', $companyId)->get(['product', 'unit', 'price']);
        }

        // Materials + stock
        $materials = $materialsStock = collect();
        if ($has('materials')) {
            $materials = \App\Models\MaterialsModel::where('company_id', $companyId)->get(['material', 'unit']);
            $materialsStock = \App\Models\MaterialsStockModel::where('materials_stock.company_id', $companyId)->join('materials', 'materials_stock.material_id', '=', 'materials.mid')->selectRaw('materials.material, materials.unit, SUM(CASE WHEN materials_stock.type = "in" THEN materials_stock.qty ELSE -materials_stock.qty END) as current_stock')->groupBy('materials_stock.material_id', 'materials.material', 'materials.unit')->get();
        }

        // Machines + maintenances
        $machines = $recentMaintenances = $upcomingMaintenances = collect();
        if ($has('machines')) {
            $machines = \App\Models\MachinesModel::where('company_id', $companyId)->get(['machine', 'type']);
            $recentMaintenances = \App\Models\MaintenancesModel::where('company_id', $companyId)->where('date', '<=', $today->toDateString())->with('machine')->orderByDesc('date')->limit(10)->get(['machine_id', 'date', 'description']);
            $upcomingMaintenances = \App\Models\MaintenancesModel::where('company_id', $companyId)->where('date', '>', $today->toDateString())->with('machine')->orderBy('date')->get(['machine_id', 'date', 'description']);
        }

        // Suppliers
        $suppliers = collect();
        if ($has('suppliers')) {
            $suppliers = \App\Models\SuppliersModel::where('company_id', $companyId)->get(['supplier', 'phone', 'location']);
        }

        // ── NDERTO PROMPTON ──────────────────────────────────────────

        $base = "You are Prodflow AI, a senior business analyst assistant for a company management system. Today is " .
            $today->toDateString() . " (" . $today->isoFormat('dddd') . ").

            LANGUAGE: Detect the language of the user's message and always reply in that same language.
            The user may write in Albanian (shqip), English, or other languages — match it exactly.

            FORMATTING RULES (very important):
            - Never use markdown. No #, ##, ###, **, *, _underline_, no bullet lists with -.
            - Write in plain text only. Use numbers for lists if needed (1. 2. 3.).
            - Keep responses concise and easy to read without any special symbols.

            ANALYTICAL CAPABILITIES:
            - Identify trends, anomalies, and patterns in the data.
            - Compare periods (this month vs last month, week over week, etc.).
            - Calculate KPIs: profit margin, revenue growth, production efficiency, expense ratio.
            - Spot risks: expiring contracts, staff shortages, production below plan, high expenses.
            - Give actionable recommendations based on data — not just describe it.
            - Answer questions phrased in any way — understand intent, not just keywords.
            - If asked for a 'deep analysis' or 'overview', produce a structured plain-text report with numbered sections.
            - Never invent data. If data is missing, say so and suggest what to check.
            - For general questions unrelated to company data, answer from your own knowledge.";

        $base .= "\n\n=== LIVE COMPANY DATA (sections loaded: " . implode(', ', $sections) . ") ===\n";

        if ($has('staff')) {
            $base .= "\nSTAFF (" . $staff->count() . " total):\n";
            $base .= $staff->map(fn($s) => "- {$s->name} {$s->surname} | {$s->position} | {$s->contact}")->join("\n");
        }

        if ($has('vacations')) {
            $base .= "\n\nALL VACATION REQUESTS (" . $allVacations->count() . " total):\n";
            $base .= $allVacations->isEmpty() ? "- None" :
                $allVacations->map(fn($v) => "- {$v->staff->name} {$v->staff->surname} | {$v->start_date} to {$v->end_date} | status: {$v->status} | reason: {$v->reason}")->join("\n");
            $base .= "\nOn leave today: " . ($onLeaveToday->isEmpty() ? "Nobody" : $onLeaveToday->map(fn($v) => "{$v->staff->name} {$v->staff->surname} (until {$v->end_date})")->join(", "));
            $base .= "\nPending requests: " . ($pendingVacations->isEmpty() ? "None" : $pendingVacations->map(fn($v) => "{$v->staff->name} {$v->staff->surname} ({$v->start_date} to {$v->end_date})")->join(", "));
            $base .= "\nUpcoming this week: " . ($upcomingVacations->isEmpty() ? "None" : $upcomingVacations->map(fn($v) => "{$v->staff->name} {$v->staff->surname} (starts {$v->start_date})")->join(", "));
        }

        if ($has('contracts')) {
            $base .= "\n\nCONTRACTS: Active: {$activeContracts} | Expired: {$expiredContracts}\n";
            $base .= "Expiring in 30 days: " . ($expiringContracts->isEmpty() ? "None" : $expiringContracts->map(fn($c) => "{$c->employee->name} {$c->employee->surname} (expires {$c->end_date})")->join(", "));
        }

        if ($has('sales')) {
            $base .= "\n\nSALES THIS MONTH:\n";
            $base .= "- Revenue: " . number_format($monthRevenue, 2) . " (" . ($monthSales->count ?? 0) . " transactions)\n";
            $base .= "- Last month: " . number_format($lastMonthRevenue, 2) . " (" . ($lastMonthSales->count ?? 0) . " transactions)\n";
            if ($revenueGrowth !== null) {
                $base .= "- Trend: " . ($revenueGrowth >= 0 ? '▲' : '▼') . " {$revenueGrowth}% vs last month\n";
            }
            $base .= "- Today: " . number_format($todaySales->total ?? 0, 2) . " (" . ($todaySales->count ?? 0) . " transactions)\n";
            $base .= "- Total clients: {$totalClients}\n";
            $base .= "- Top clients: " . ($topClients->isEmpty() ? "No sales yet" : $topClients->map(fn($c) => "{$c->client} " . number_format($c->total, 2) . " ({$c->orders} orders)")->join(", ")) . "\n";
            $base .= "- Top products: " . ($topProducts->isEmpty() ? "No sales" : $topProducts->map(fn($p) => "{$p->product} " . number_format($p->total_value, 2) . " ({$p->total_qty} units)")->join(", ")) . "\n";
            $base .= "- Sales trend (6 months):\n";
            foreach ($salesTrend as $row) {
                $base .= "  " . date('M Y', mktime(0, 0, 0, $row->mo, 1, $row->yr)) . ": " . number_format($row->total, 2) . " ({$row->count} tx)\n";
            }
        }

        if ($has('expenses')) {
            $base .= "\nEXPENSES THIS MONTH:\n";
            $base .= "- Total: " . number_format($monthExpTotal, 2) . " (" . ($monthExpenses->count ?? 0) . " entries)\n";
            $base .= "- Last month: " . number_format($lastMonthExpTotal, 2) . "\n";
            if ($expenseGrowth !== null) {
                $base .= "- Trend: " . ($expenseGrowth >= 0 ? '▲' : '▼') . " {$expenseGrowth}% vs last month\n";
            }
            $base .= "- Recent: " . $recentExpenses->map(fn($e) => "{$e->comment} {$e->price} ({$e->date})")->join(", ") . "\n";
            $base .= "- Expenses trend (6 months):\n";
            foreach ($expensesTrend as $row) {
                $base .= "  " . date('M Y', mktime(0, 0, 0, $row->mo, 1, $row->yr)) . ": " . number_format($row->total, 2) . "\n";
            }
        }

        if ($has('financial') || $has('sales')) {
            $base .= "\nFINANCIAL OVERVIEW:\n";
            $base .= "- Revenue: " . number_format($monthRevenue, 2) . " | Expenses: " . number_format($monthExpTotal, 2) . " | Profit: " . number_format($profit, 2) . "\n";
            $base .= "- Profit margin: " . ($monthRevenue > 0 ? round(($profit / $monthRevenue) * 100, 1) . "%" : "N/A") . "\n";
            $base .= "- Last month profit: " . number_format($lastMonthProfit, 2) . "\n";
            if ($profitGrowth !== null) {
                $base .= "- Profit trend: " . ($profitGrowth >= 0 ? '▲' : '▼') . " {$profitGrowth}% vs last month\n";
            }
        }

        if ($has('production')) {
            $base .= "\nPRODUCTION:\n";
            $base .= "- Today: {$todayProduction} | This month: {$monthProduction} | Last month: {$lastMonthProduction} units\n";
            if ($productionGrowth !== null) {
                $base .= "- Trend: " . ($productionGrowth >= 0 ? '▲' : '▼') . " {$productionGrowth}% vs last month\n";
            }
            $base .= "- Trend (6 months):\n";
            foreach ($productionTrend as $row) {
                $base .= "  " . date('M Y', mktime(0, 0, 0, $row->mo, 1, $row->yr)) . ": {$row->total} units\n";
            }
            $base .= "- Active plans: " . ($activePlans->isEmpty() ? "None" :
                $activePlans->map(fn($p) => "Product#{$p->product_id} {$p->planned_qty}u [{$p->start_date}→{$p->end_date}] {$p->status}")->join(", ")) . "\n";
        }

        if ($has('products')) {
            $base .= "\nPRODUCTS:\n";
            $base .= $products->map(fn($p) => "- {$p->product} | {$p->unit} | price: {$p->price}")->join("\n");
        }

        if ($has('materials')) {
            $base .= "\nMATERIALS & STOCK:\n";
            $base .= $materialsStock->isEmpty()
                ? $materials->map(fn($m) => "- {$m->material} ({$m->unit})")->join("\n")
                : $materialsStock->map(fn($s) => "- {$s->material}: {$s->current_stock} {$s->unit} in stock")->join("\n");
        }

        if ($has('machines')) {
            $base .= "\nMACHINES:\n";
            $base .= $machines->map(fn($m) => "- {$m->machine} (type: {$m->type})")->join("\n");
            $base .= "\nUpcoming maintenances: " . ($upcomingMaintenances->isEmpty() ? "None" :
                $upcomingMaintenances->map(fn($m) => "{$m->machine->machine} on {$m->date}")->join(", "));
            $base .= "\nRecent maintenances: " . ($recentMaintenances->isEmpty() ? "None" :
                $recentMaintenances->map(fn($m) => "{$m->machine->machine} on {$m->date} — {$m->description}")->join(", "));
        }

        if ($has('suppliers')) {
            $base .= "\nSUPPLIERS:\n";
            $base .= $suppliers->isEmpty() ? "- None registered" :
                $suppliers->map(fn($s) => "- {$s->supplier} | {$s->phone} | {$s->location}")->join("\n");
        }

        return $base;
    }

    public function alerts(Request $request)
    {
        $request->validate(['company_id' => 'required|integer']);
        $companyId = $request->company_id;
        $today = now();
        $alerts = [];

        // Kontratat që skadojnë brenda 30 ditëve
        $expiringContracts = \App\Models\ContractsModel::where('company_id', $companyId)
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->where('end_date', '<=', $today->copy()->addDays(30)->toDateString())
            ->where('end_date', '>=', $today->toDateString())
            ->with('employee')
            ->get();

        foreach ($expiringContracts as $c) {
            $daysLeft = round($today->diffInDays($c->end_date));
            $alerts[] = [
                'type' => 'warning',
                'category' => 'contracts',
                'message' => "Contract of {$c->employee->name} {$c->employee->surname} expires in {$daysLeft} days ({$c->end_date})",
            ];
        }

        // Kërkesat e pushimit në pritje
        $pendingVacations = \App\Models\VacationsModel::where('company_id', $companyId)
            ->where('status', 'pending')
            ->with('staff')
            ->get();

        if ($pendingVacations->isNotEmpty()) {
            $names = $pendingVacations->map(fn($v) => "{$v->staff->name} {$v->staff->surname}")->join(', ');
            $alerts[] = [
                'type' => 'info',
                'category' => 'vacations',
                'message' => "{$pendingVacations->count()} vacation request awaiting confirmation: {$names}",
            ];
        }

        // Mirëmbajtjet e ardhshme
        $upcomingMaintenances = \App\Models\MaintenancesModel::where('company_id', $companyId)
            ->where('date', '>', $today->toDateString())
            ->where('date', '<=', $today->copy()->addDays(7)->toDateString())
            ->with('machine')
            ->orderBy('date')
            ->get();

        foreach ($upcomingMaintenances as $m) {
            $alerts[] = [
                'type' => 'info',
                'category' => 'maintenance',
                'message' => "Planned Maintenance: {$m->machine->machine} on {$m->date} — {$m->description}",
            ];
        }

        $lowStock = \App\Models\MaterialsStockModel::where('materials_stock.company_id', $companyId)
            ->join('materials', 'materials_stock.material_id', '=', 'materials.mid')
            ->selectRaw('materials.material,
                SUM(CASE WHEN materials_stock.type = "in" THEN materials_stock.qty ELSE -materials_stock.qty END) as current_stock')
            ->groupBy('materials_stock.material_id', 'materials.material')
            ->havingRaw('current_stock <= 0')
            ->get();

        foreach ($lowStock as $s) {
            $alerts[] = [
                'type' => 'danger',
                'category' => 'stock',
                'message' => "Stock \"{$s->material}\" is {$s->current_stock}, reorder immediately!",
            ];
        }

        // Planet e prodhimit me vonesë (status pending/in_progress, end_date kaloi)
        $delayedPlans = \App\Models\PlanificationModel::join('products', 'planification.product_id', '=', 'products.pid')
            ->where('planification.company_id', $companyId)
            ->whereIn('planification.status', ['pending', 'in_progress'])
            ->where('planification.end_date', '<', $today->toDateString())
            ->get(['products.product', 'planification.planned_qty', 'planification.end_date', 'planification.status']);

        foreach ($delayedPlans as $p) {
            $alerts[] = [
                'type' => 'danger',
                'category' => 'production',
                'message' => "Delayed production plan: {$p->product} ({$p->planned_qty} units) — should have been completed by {$p->end_date}",
            ];
        }

        return response()->json([
            'alerts' => $alerts,
            'count' => count($alerts),
        ]);
    }

    private function mentions(string $message, array $keywords): bool
    {
        $message = strtolower($message);
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword))
                return true;
        }
        return false;
    }
}