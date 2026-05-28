<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth', description: 'Authentication endpoints')]
class AuthSwagger
{
    #[OA\Post(
        path: '/api/login',
        tags: ['Auth'],
        summary: 'Login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['username', 'password'],
                    properties: [
                        new OA\Property(property: 'username', type: 'string', description: 'The username of the user'),
                        new OA\Property(property: 'password', type: 'string', description: 'The password of the user'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Success'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function login(): void {}

    #[OA\Get(path: '/api/me', tags: ['Auth'], summary: 'Current user', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success'), new OA\Response(response: 401, description: 'Unauthorized')])]
    public function me(): void {}

    #[OA\Post(path: '/api/logout', tags: ['Auth'], summary: 'Logout', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success'), new OA\Response(response: 401, description: 'Unauthorized')])]
    public function logout(): void {}
}
