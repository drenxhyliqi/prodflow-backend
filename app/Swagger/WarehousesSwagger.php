<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Warehouses', description: 'Warehouses management')]
class WarehousesSwagger
{
    #[OA\Post(path: '/api/admin/create_warehouse', tags: ['Warehouses'], summary: 'Create warehouse', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createWarehouse(): void {}
    #[OA\Get(path: '/api/admin/warehouses', tags: ['Warehouses'], summary: 'List warehouses', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function warehouses(): void {}
    #[OA\Get(path: '/api/admin/edit_warehouse/{id}', tags: ['Warehouses'], summary: 'Get warehouse by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editWarehouse(): void {}
    #[OA\Post(path: '/api/admin/update_warehouse', tags: ['Warehouses'], summary: 'Update warehouse', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateWarehouse(): void {}
    #[OA\Get(path: '/api/admin/delete_warehouse/{id}', tags: ['Warehouses'], summary: 'Delete warehouse', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteWarehouse(): void {}
}
