<?php

namespace App\Repositories;

use App\Models\CompaniesModel;
use Illuminate\Support\Facades\DB;

class ReportsRepository
{
    protected string $table;
    public function __construct(CompaniesModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function getProductsStock(int $limit, int $companyId)
    {
        return DB::table('products as p')
            ->leftJoin('sales as s', 'p.pid', '=', 's.product_id')
            ->leftJoin('production as pr', 'p.pid', '=', 'pr.product_id')
            ->select(
                'p.pid',
                'p.product',
                'p.unit',
                DB::raw('
                    COALESCE(SUM(pr.qty), 0) - COALESCE(SUM(s.qty), 0) as product_stock
                ')
            )
            ->where('p.company_id', $companyId)
            ->groupBy('p.pid', 'p.product', 'p.unit')
            ->orderByDesc('p.pid')
            ->paginate($limit);
    }
    //---------------
    public function getSearchedProductsStock(string $search, int $companyId, int $limit)
    {
        return DB::table('products as p')
            ->leftJoin('sales as s', 'p.pid', '=', 's.product_id')
            ->leftJoin('production as pr', 'p.pid', '=', 'pr.product_id')
            ->select(
                'p.pid',
                'p.product',
                'p.unit',
                DB::raw('
                    COALESCE(SUM(pr.qty), 0) - COALESCE(SUM(s.qty), 0) as product_stock
                ')
            )
            ->where('p.product', 'like', "%{$search}%")
            ->where('p.company_id', $companyId)
            ->groupBy('p.pid', 'p.product', 'p.unit')
            ->orderByDesc('p.pid')
            ->paginate($limit);
    }
}
