<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Clients', description: 'Clients management')]
class ClientsSwagger
{
    #[OA\Post(path: '/api/admin/create_client', tags: ['Clients'], summary: 'Create client', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createClient(): void {}
    #[OA\Get(path: '/api/admin/clients', tags: ['Clients'], summary: 'List clients', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function clients(): void {}
    #[OA\Get(path: '/api/admin/edit_client/{id}', tags: ['Clients'], summary: 'Get client by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editClient(): void {}
    #[OA\Post(path: '/api/admin/update_client', tags: ['Clients'], summary: 'Update client', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateClient(): void {}
    #[OA\Get(path: '/api/admin/delete_client/{id}', tags: ['Clients'], summary: 'Delete client', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteClient(): void {}
}
