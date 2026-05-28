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
    #[OA\Get(path: '/api/invitations/{token}', tags: ['Auth'], summary: 'Validate invitation token', parameters: [new OA\Parameter(name: 'token', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Valid invitation'), new OA\Response(response: 404, description: 'Invalid or expired invitation')])]
    public function validateInvitationToken(): void {}
    #[OA\Post(path: '/api/invitations/accept', tags: ['Auth'], summary: 'Accept invitation and set password', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Invitation accepted'), new OA\Response(response: 422, description: 'Validation or invitation error')])]
    public function acceptInvitation(): void {}

    #[OA\Get(path: '/api/me', tags: ['Auth'], summary: 'Current user', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success'), new OA\Response(response: 401, description: 'Unauthorized')])]
    public function me(): void {}

    #[OA\Post(path: '/api/logout', tags: ['Auth'], summary: 'Logout', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success'), new OA\Response(response: 401, description: 'Unauthorized')])]
    public function logout(): void {}
}
