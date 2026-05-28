<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Orders', description: 'Orders management')]
class OrdersSwagger
{
    #[OA\Post(path: '/api/admin/create_order', tags: ['Orders'], summary: 'Create order', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created'), new OA\Response(response: 422, description: 'Validation error')])]
    public function createOrder(): void {}
    #[OA\Get(path: '/api/admin/orders', tags: ['Orders'], summary: 'List orders', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function orders(): void {}
    #[OA\Get(path: '/api/admin/edit_order/{order_number}', tags: ['Orders'], summary: 'Get order by number', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'order_number', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editOrder(): void {}
    #[OA\Post(path: '/api/admin/update_order', tags: ['Orders'], summary: 'Update order', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateOrder(): void {}
    #[OA\Get(path: '/api/admin/delete_order/{order_number}', tags: ['Orders'], summary: 'Delete order', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'order_number', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteOrder(): void {}
    #[OA\Post(path: '/api/admin/convert_order_to_sale/{order_number}', tags: ['Orders'], summary: 'Convert order to sale', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'order_number', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Converted')])]
    public function convertOrderToSale(): void {}
}
