<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsCacheService;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    protected AnalyticsCacheService $cacheService;

    public function __construct(AnalyticsCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        return response()->json(array_merge(
            $this->cacheService->getDashboard($companyId),
            ['background_refresh' => $this->cacheService->getRefreshStatus($companyId)]
        ));
    }

    public function refreshStatus(Request $request)
    {
        return response()->json(
            $this->cacheService->getRefreshStatus($request->user()->company_id)
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

        return response()->json(
            AnalyticsCacheService::withBackgroundRefresh([
                'success' => true,
                'message' => 'Recent activity cleared.',
            ], $companyId)
        );
    }
}
