<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Trucks', description: 'Trucks management')]
class TrucksSwagger
{
    #[OA\Post(path: '/api/admin/create_truck', tags: ['Trucks'], summary: 'Create truck', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createTruck(): void {}
    #[OA\Get(path: '/api/admin/trucks', tags: ['Trucks'], summary: 'List trucks', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function trucks(): void {}
    #[OA\Get(path: '/api/admin/edit_truck/{id}', tags: ['Trucks'], summary: 'Get truck by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editTruck(): void {}
    #[OA\Post(path: '/api/admin/update_truck', tags: ['Trucks'], summary: 'Update truck', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateTruck(): void {}
    #[OA\Get(path: '/api/admin/delete_truck/{id}', tags: ['Trucks'], summary: 'Delete truck', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteTruck(): void {}
    #[OA\Get(path: '/api/admin/change_truck_status/{id}', tags: ['Trucks'], summary: 'Change truck status', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function changeTruckStatus(): void {}
}
