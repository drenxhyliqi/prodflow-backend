<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'AI', description: 'AI endpoints')]
class AiSwagger
{
    #[OA\Post(path: '/api/ai/chat', tags: ['AI'], summary: 'AI chat', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Success')])]
    public function aiChat(): void {}
    #[OA\Post(path: '/api/ai/chat-data', tags: ['AI'], summary: 'AI chat with data', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Success')])]
    public function aiChatData(): void {}
    #[OA\Post(path: '/api/ai/analyze-text', tags: ['AI'], summary: 'Analyze text with AI', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Success')])]
    public function aiAnalyzeText(): void {}
    #[OA\Post(path: '/api/ai/alerts', tags: ['AI'], summary: 'Generate AI alerts', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(type: 'object')), responses: [new OA\Response(response: 200, description: 'Success')])]
    public function aiAlerts(): void {}
}
