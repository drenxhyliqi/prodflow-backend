<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Reports', description: 'Reports endpoints')]
#[OA\Tag(name: 'ProductionReports', description: 'Production report endpoints')]
#[OA\Tag(name: 'SalesReports', description: 'Sales report endpoints')]
class ReportsSwagger
{
    #[OA\Get(path: '/api/admin/products_stock', tags: ['Reports'], summary: 'Products stock report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function productsStock(): void {}
    #[OA\Post(path: '/api/admin/reports/batch', tags: ['Reports'], summary: 'Start report batch', security: [['sanctum' => []]], responses: [new OA\Response(response: 201, description: 'Batch started')])]
    public function startBatch(): void {}
    #[OA\Get(path: '/api/admin/reports/batch/{id}', tags: ['Reports'], summary: 'Batch status', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function batchStatus(): void {}
    #[OA\Get(path: '/api/admin/reports/runs/{id}/access', tags: ['Reports'], summary: 'Report run access check', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function runAccess(): void {}
    #[OA\Get(path: '/api/admin/reports/production/summary', tags: ['ProductionReports'], summary: 'Production summary report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function productionSummary(): void {}
    #[OA\Get(path: '/api/admin/reports/production/trends', tags: ['ProductionReports'], summary: 'Production trends report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function productionTrends(): void {}
    #[OA\Get(path: '/api/admin/reports/production/machines', tags: ['ProductionReports'], summary: 'Production by machines report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function productionMachines(): void {}
    #[OA\Get(path: '/api/admin/reports/production/top-products', tags: ['ProductionReports'], summary: 'Top produced products report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function productionTopProducts(): void {}
    #[OA\Get(path: '/api/admin/reports/production/status-distribution', tags: ['ProductionReports'], summary: 'Production status distribution', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function productionStatusDistribution(): void {}
    #[OA\Get(path: '/api/admin/reports/sales/summary', tags: ['SalesReports'], summary: 'Sales summary report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function salesSummary(): void {}
    #[OA\Get(path: '/api/admin/reports/sales/trends', tags: ['SalesReports'], summary: 'Sales trends report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function salesTrends(): void {}
    #[OA\Get(path: '/api/admin/reports/sales/top-products', tags: ['SalesReports'], summary: 'Top sold products report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function salesTopProducts(): void {}
    #[OA\Get(path: '/api/admin/reports/sales/top-clients', tags: ['SalesReports'], summary: 'Top clients report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function salesTopClients(): void {}
    #[OA\Get(path: '/api/admin/reports/sales/orders-overview', tags: ['SalesReports'], summary: 'Orders overview report', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function salesOrdersOverview(): void {}
}
