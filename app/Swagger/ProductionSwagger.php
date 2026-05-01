<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Production', description: 'Production management')]
class ProductionSwagger
{
    #[OA\Post(path: '/api/admin/create_production', tags: ['Production'], summary: 'Create production', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createProduction(): void {}
    #[OA\Get(path: '/api/admin/production', tags: ['Production'], summary: 'List production', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function production(): void {}
    #[OA\Get(path: '/api/admin/edit_production/{id}', tags: ['Production'], summary: 'Get production by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editProduction(): void {}
    #[OA\Post(path: '/api/admin/update_production', tags: ['Production'], summary: 'Update production', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateProduction(): void {}
    #[OA\Get(path: '/api/admin/delete_production/{id}', tags: ['Production'], summary: 'Delete production', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteProduction(): void {}
}
