<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Machines', description: 'Machines management')]
class MachinesSwagger
{
    #[OA\Post(path: '/api/admin/create_machine', tags: ['Machines'], summary: 'Create machine', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createMachine(): void {}
    #[OA\Get(path: '/api/admin/machines', tags: ['Machines'], summary: 'List machines', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function machines(): void {}
    #[OA\Get(path: '/api/admin/edit_machine/{id}', tags: ['Machines'], summary: 'Get machine by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editMachine(): void {}
    #[OA\Post(path: '/api/admin/update_machine', tags: ['Machines'], summary: 'Update machine', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateMachine(): void {}
    #[OA\Get(path: '/api/admin/delete_machine/{id}', tags: ['Machines'], summary: 'Delete machine', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteMachine(): void {}
}
