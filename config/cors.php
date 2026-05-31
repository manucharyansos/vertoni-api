<?php

$defaultAllowedOrigins = [
    'https://verthoni.com',
    'https://www.verthoni.com',
    'https://backend.verthoni.com',
    'http://localhost:3000',
    'http://127.0.0.1:3000',
];

$envAllowedOrigins = array_filter(array_map(
    'trim',
    explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))
));

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => $envAllowedOrigins ?: $defaultAllowedOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => filter_var(env('CORS_SUPPORTS_CREDENTIALS', false), FILTER_VALIDATE_BOOL),
];
