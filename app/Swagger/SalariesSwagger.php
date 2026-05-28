<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Salaries', description: 'Salaries management')]
class SalariesSwagger
{
    #[OA\Post(path: '/api/admin/create_salary', tags: ['Salaries'], summary: 'Create salary', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createSalary(): void {}
    #[OA\Get(path: '/api/admin/salaries', tags: ['Salaries'], summary: 'List salaries', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function salaries(): void {}
    #[OA\Get(path: '/api/admin/edit_salary/{id}', tags: ['Salaries'], summary: 'Get salary by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editSalary(): void {}
    #[OA\Post(path: '/api/admin/update_salary', tags: ['Salaries'], summary: 'Update salary', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateSalary(): void {}
    #[OA\Get(path: '/api/admin/delete_salary/{id}', tags: ['Salaries'], summary: 'Delete salary', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteSalary(): void {}
}
