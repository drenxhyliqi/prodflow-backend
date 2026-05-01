<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Products', description: 'Products management')]
class ProductsSwagger
{
    #[OA\Post(path: '/api/admin/create_product', tags: ['Products'], summary: 'Create product', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createProduct(): void {}
    #[OA\Get(path: '/api/admin/products', tags: ['Products'], summary: 'List products', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function products(): void {}
    #[OA\Get(path: '/api/admin/edit_product/{id}', tags: ['Products'], summary: 'Get product by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editProduct(): void {}
    #[OA\Post(path: '/api/admin/update_product', tags: ['Products'], summary: 'Update product', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateProduct(): void {}
    #[OA\Get(path: '/api/admin/delete_product/{id}', tags: ['Products'], summary: 'Delete product', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteProduct(): void {}
}
