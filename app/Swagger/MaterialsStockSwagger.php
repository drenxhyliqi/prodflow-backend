<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'MaterialsStock', description: 'Materials stock management')]
class MaterialsStockSwagger
{
    #[OA\Post(path: '/api/admin/create_materials_stock', tags: ['MaterialsStock'], summary: 'Create materials stock', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createMaterialsStock(): void {}
    #[OA\Get(path: '/api/admin/materials_stock', tags: ['MaterialsStock'], summary: 'List materials stock', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function materialsStock(): void {}
    #[OA\Get(path: '/api/admin/edit_materials_stock/{id}', tags: ['MaterialsStock'], summary: 'Get materials stock by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editMaterialsStock(): void {}
    #[OA\Post(path: '/api/admin/update_materials_stock', tags: ['MaterialsStock'], summary: 'Update materials stock', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateMaterialsStock(): void {}
    #[OA\Get(path: '/api/admin/delete_materials_stock/{id}', tags: ['MaterialsStock'], summary: 'Delete materials stock', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteMaterialsStock(): void {}
}
