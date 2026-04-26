<?php

$defaultOrigins = implode(',', array_filter([
    env('FRONTEND_URL', 'http://localhost:3000'),
    env('ADMIN_URL', 'http://localhost:8000'),
]));

return [
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', $defaultOrigins))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
