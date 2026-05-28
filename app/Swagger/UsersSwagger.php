<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Users', description: 'Users management')]
class UsersSwagger
{
    #[OA\Post(path: '/api/admin/create_user', tags: ['Users'], summary: 'Create user', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createUser(): void {}
    #[OA\Get(path: '/api/admin/users', tags: ['Users'], summary: 'List users', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function users(): void {}
    #[OA\Get(path: '/api/admin/edit_user/{id}', tags: ['Users'], summary: 'Get user by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editUser(): void {}
    #[OA\Post(path: '/api/admin/update_user', tags: ['Users'], summary: 'Update user', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateUser(): void {}
    #[OA\Get(path: '/api/admin/delete_user/{id}', tags: ['Users'], summary: 'Delete user', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteUser(): void {}
    #[OA\Post(path: '/api/admin/update_account', tags: ['Users'], summary: 'Update current account', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateAccount(): void {}
}
