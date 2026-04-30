<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Companies', description: 'Companies management')]
class CompaniesSwagger
{
    #[OA\Post(path: '/api/admin/create_company', tags: ['Companies'], summary: 'Create company', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 201, description: 'Created'), new OA\Response(response: 422, description: 'Validation error')])]
    public function createCompany(): void {}
    #[OA\Get(path: '/api/admin/companies', tags: ['Companies'], summary: 'List companies', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function companies(): void {}
    #[OA\Get(path: '/api/admin/all_companies', tags: ['Companies'], summary: 'List all companies', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function allCompanies(): void {}
    #[OA\Get(path: '/api/admin/active_company', tags: ['Companies'], summary: 'Get active company', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success')])]
    public function activeCompany(): void {}
    #[OA\Post(path: '/api/admin/set_active_company/{id}', tags: ['Companies'], summary: 'Set active company', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success'), new OA\Response(response: 404, description: 'Not found')])]
    public function setActiveCompany(): void {}
    #[OA\Get(path: '/api/admin/edit_company/{id}', tags: ['Companies'], summary: 'Get company by id', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Success'), new OA\Response(response: 404, description: 'Not found')])]
    public function editCompany(): void {}
    #[OA\Post(path: '/api/admin/update_company', tags: ['Companies'], summary: 'Update company', security: [['sanctum' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Updated'), new OA\Response(response: 422, description: 'Validation error')])]
    public function updateCompany(): void {}
    #[OA\Get(path: '/api/admin/delete_company/{id}', tags: ['Companies'], summary: 'Delete company', security: [['sanctum' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Deleted'), new OA\Response(response: 404, description: 'Not found')])]
    public function deleteCompany(): void {}
}
