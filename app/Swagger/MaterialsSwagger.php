<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Materials', description: 'Materials management')]
class MaterialsSwagger
{
    #[OA\Post(path: '/api/admin/create_material', tags: ['Materials'], summary: 'Create material', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createMaterial(): void {}
    #[OA\Get(path: '/api/admin/materials', tags: ['Materials'], summary: 'List materials', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function materials(): void {}
    #[OA\Get(path: '/api/admin/edit_material/{id}', tags: ['Materials'], summary: 'Get material by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editMaterial(): void {}
    #[OA\Post(path: '/api/admin/update_material', tags: ['Materials'], summary: 'Update material', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateMaterial(): void {}
    #[OA\Get(path: '/api/admin/delete_material/{id}', tags: ['Materials'], summary: 'Delete material', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteMaterial(): void {}
}
