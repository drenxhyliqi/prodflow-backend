<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Dashboard', description: 'Dashboard analytics')]
class DashboardSwagger
{
    #[OA\Get(path: '/api/admin/dashboard', tags: ['Dashboard'], summary: 'Get dashboard metrics', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function dashboard(): void {}
}
