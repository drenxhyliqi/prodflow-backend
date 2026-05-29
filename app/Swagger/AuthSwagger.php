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
    #[OA\Get(path: '/api/signup-available', tags: ['Auth'], summary: 'Check if first-user signup is available', responses: [new OA\Response(response: 200, description: 'Returns { available: true|false }')])]
    public function signupAvailable(): void {}
    #[OA\Post(path: '/api/signup', tags: ['Auth'], summary: 'Register the first admin user and company', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object', required: ['user', 'username', 'password', 'name', 'sector', 'location'], properties: [new OA\Property(property: 'user', type: 'string'), new OA\Property(property: 'username', type: 'string'), new OA\Property(property: 'password', type: 'string'), new OA\Property(property: 'name', type: 'string', description: 'Company name'), new OA\Property(property: 'sector', type: 'string'), new OA\Property(property: 'location', type: 'string')])), responses: [new OA\Response(response: 201, description: 'User created and logged in'), new OA\Response(response: 403, description: 'Signup not available'), new OA\Response(response: 422, description: 'Validation error')])]
    public function signup(): void {}
    #[OA\Get(path: '/api/invitations/{token}', tags: ['Auth'], summary: 'Validate invitation token', parameters: [new OA\Parameter(name: 'token', in: 'path', required: true, schema: new OA\Schema(type: 'string'))], responses: [new OA\Response(response: 200, description: 'Valid invitation'), new OA\Response(response: 404, description: 'Invalid or expired invitation')])]
    public function validateInvitationToken(): void {}
    #[OA\Post(path: '/api/invitations/accept', tags: ['Auth'], summary: 'Accept invitation and set password', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Invitation accepted'), new OA\Response(response: 422, description: 'Validation or invitation error')])]
    public function acceptInvitation(): void {}

    #[OA\Get(path: '/api/me', tags: ['Auth'], summary: 'Current user', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success'), new OA\Response(response: 401, description: 'Unauthorized')])]
    public function me(): void {}

    #[OA\Post(path: '/api/logout', tags: ['Auth'], summary: 'Logout', security: [['sanctum' => []]], responses: [new OA\Response(response: 200, description: 'Success'), new OA\Response(response: 401, description: 'Unauthorized')])]
    public function logout(): void {}
}
