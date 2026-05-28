<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Vacations', description: 'Vacations management')]
class VacationsSwagger
{
    #[OA\Post(path: '/api/admin/create_vacation', tags: ['Vacations'], summary: 'Create vacation', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createVacation(): void {}
    #[OA\Get(path: '/api/admin/vacations', tags: ['Vacations'], summary: 'List vacations', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function vacations(): void {}
    #[OA\Get(path: '/api/admin/edit_vacation/{id}', tags: ['Vacations'], summary: 'Get vacation by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editVacation(): void {}
    #[OA\Post(path: '/api/admin/update_vacation', tags: ['Vacations'], summary: 'Update vacation', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateVacation(): void {}
    #[OA\Get(path: '/api/admin/delete_vacation/{id}', tags: ['Vacations'], summary: 'Delete vacation', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteVacation(): void {}
}
