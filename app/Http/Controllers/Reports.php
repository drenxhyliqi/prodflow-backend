<?php

namespace App\Http\Controllers;

use App\Services\ReportsService;
use Illuminate\Http\Request;

class Reports extends Controller
{
    protected ReportsService $service;
    public function __construct(ReportsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function productsStock(Request $request)
    {
        $companyId = $request->user()->company_id;
        $search = $request->query('search', '');
        return response()->json(
            $this->service->getProductsStock(10, $companyId, $search)
        );
    }
}
