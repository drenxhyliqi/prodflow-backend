<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Planification', description: 'Planification management')]
class PlanificationSwagger
{
    #[OA\Post(path: '/api/admin/create_planification', tags: ['Planification'], summary: 'Create planification', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createPlanification(): void
    {
    }

    #[OA\Get(path: '/api/admin/planification', tags: ['Planification'], summary: 'List planification', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function planification(): void
    {
    }

    #[OA\Get(path: '/api/admin/edit_planification/{id}', tags: ['Planification'], summary: 'Get planification by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editPlanification(): void
    {
    }

    #[OA\Post(path: '/api/admin/update_planification', tags: ['Planification'], summary: 'Update planification', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updatePlanification(): void
    {
    }

    #[OA\Get(path: '/api/admin/delete_planification/{id}', tags: ['Planification'], summary: 'Delete planification', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deletePlanification(): void
    {
    }
}
