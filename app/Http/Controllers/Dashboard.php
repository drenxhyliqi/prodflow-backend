<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index(Request $request)
    {
        return response()->json(
            $this->dashboardService->build($request->user()->company_id)
        );
    }

    public function clearActivity(Request $request)
    {
        $companyId = $request->user()->company_id;

        $thresholds = [
            'sid' => (int) \App\Models\SalesModel::where('company_id', $companyId)->max('sid'),
            'pid' => (int) \App\Models\ProductionModel::where('company_id', $companyId)->max('pid'),
            'vid' => (int) \App\Models\VacationsModel::where('company_id', $companyId)->max('vid'),
            'mid' => (int) \App\Models\MaintenancesModel::where('company_id', $companyId)->max('mid'),
        ];

        \Illuminate\Support\Facades\Cache::put("activity_clear_{$companyId}", $thresholds, now()->addDays(7));

        return response()->json([
            'success' => true,
            'message' => 'Recent activity cleared.',
        ]);
    }
}
