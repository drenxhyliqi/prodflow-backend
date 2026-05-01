<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Staff', description: 'Staff management')]
class StaffSwagger
{
    #[OA\Post(path: '/api/admin/create_staff', tags: ['Staff'], summary: 'Create staff', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createStaff(): void {}
    #[OA\Get(path: '/api/admin/staff', tags: ['Staff'], summary: 'List staff', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function staff(): void {}
    #[OA\Get(path: '/api/admin/edit_staff/{id}', tags: ['Staff'], summary: 'Get staff by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editStaff(): void {}
    #[OA\Post(path: '/api/admin/update_staff', tags: ['Staff'], summary: 'Update staff', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateStaff(): void {}
    #[OA\Get(path: '/api/admin/delete_staff/{id}', tags: ['Staff'], summary: 'Delete staff', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteStaff(): void {}
}
