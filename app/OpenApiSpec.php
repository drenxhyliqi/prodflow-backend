<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Prodflow Backend API',
    version: '1.0.0',
    description: 'API documentation for Prodflow backend.'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Paste only your access token'
)]
class OpenApiSpec
{
}
