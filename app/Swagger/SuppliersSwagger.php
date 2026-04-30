<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Suppliers', description: 'Suppliers management')]
class SuppliersSwagger
{
    #[OA\Post(path: '/api/admin/create_supplier', tags: ['Suppliers'], summary: 'Create supplier', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createSupplier(): void {}
    #[OA\Get(path: '/api/admin/suppliers', tags: ['Suppliers'], summary: 'List suppliers', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function suppliers(): void {}
    #[OA\Get(path: '/api/admin/edit_supplier/{id}', tags: ['Suppliers'], summary: 'Get supplier by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editSupplier(): void {}
    #[OA\Post(path: '/api/admin/update_supplier', tags: ['Suppliers'], summary: 'Update supplier', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateSupplier(): void {}
    #[OA\Get(path: '/api/admin/delete_supplier/{id}', tags: ['Suppliers'], summary: 'Delete supplier', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteSupplier(): void {}
}
