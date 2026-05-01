<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Expenses', description: 'Expenses management')]
class ExpensesSwagger
{
    #[OA\Post(path: '/api/admin/create_expense', tags: ['Expenses'], summary: 'Create expense', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created')])]
    public function createExpense(): void {}
    #[OA\Get(path: '/api/admin/expenses', tags: ['Expenses'], summary: 'List expenses', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')), new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function expenses(): void {}
    #[OA\Get(path: '/api/admin/edit_expense/{id}', tags: ['Expenses'], summary: 'Get expense by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function editExpense(): void {}
    #[OA\Post(path: '/api/admin/update_expense', tags: ['Expenses'], summary: 'Update expense', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated')])]
    public function updateExpense(): void {}
    #[OA\Get(path: '/api/admin/delete_expense/{id}', tags: ['Expenses'], summary: 'Delete expense', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted')])]
    public function deleteExpense(): void {}
}
