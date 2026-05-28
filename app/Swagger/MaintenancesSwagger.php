<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Maintenances', description: 'Maintenances management')]
class MaintenancesSwagger
{
    #[OA\Post(path: '/api/admin/create_maintenance', tags: ['Maintenances'], summary: 'Create maintenance', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createMaintenance(): void {}
    #[OA\Get(path: '/api/admin/maintenances', tags: ['Maintenances'], summary: 'List maintenances', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function maintenances(): void {}
    #[OA\Get(path: '/api/admin/edit_maintenance/{id}', tags: ['Maintenances'], summary: 'Get maintenance by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editMaintenance(): void {}
    #[OA\Post(path: '/api/admin/update_maintenance', tags: ['Maintenances'], summary: 'Update maintenance', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateMaintenance(): void {}
    #[OA\Get(path: '/api/admin/delete_maintenance/{id}', tags: ['Maintenances'], summary: 'Delete maintenance', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteMaintenance(): void {}
}
