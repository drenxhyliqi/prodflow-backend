<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Sales', description: 'Sales management')]
class SalesSwagger
{
    #[OA\Post(path: '/api/admin/create_sale', tags: ['Sales'], summary: 'Create sale', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createSale(): void {}
    #[OA\Get(path: '/api/admin/sales', tags: ['Sales'], summary: 'List sales', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function sales(): void {}
    #[OA\Get(path: '/api/admin/edit_sale/{sale_number}', tags: ['Sales'], summary: 'Get sale by sale number', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'sale_number', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editSale(): void {}
    #[OA\Post(path: '/api/admin/update_sale/{sale_number}', tags: ['Sales'], summary: 'Update sale', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'sale_number', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateSale(): void {}
    #[OA\Get(path: '/api/admin/delete_sale/{sale_number}', tags: ['Sales'], summary: 'Delete sale', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'sale_number', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteSale(): void {}
    #[OA\Get(path: '/api/admin/invoice/{sale_number}', tags: ['Sales'], summary: 'Get sale invoice', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'sale_number', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function invoice(): void {}
}
