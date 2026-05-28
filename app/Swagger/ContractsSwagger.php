<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Contracts', description: 'Contracts management')]
class ContractsSwagger
{
    #[OA\Post(path: '/api/admin/create_contract', tags: ['Contracts'], summary: 'Create contract', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createContract(): void {}
    #[OA\Get(path: '/api/admin/contracts', tags: ['Contracts'], summary: 'List contracts', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function contracts(): void {}
    #[OA\Get(path: '/api/admin/edit_contract/{id}', tags: ['Contracts'], summary: 'Get contract by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editContract(): void {}
    #[OA\Post(path: '/api/admin/update_contract', tags: ['Contracts'], summary: 'Update contract', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateContract(): void {}
    #[OA\Get(path: '/api/admin/delete_contract/{id}', tags: ['Contracts'], summary: 'Delete contract', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteContract(): void {}
}
