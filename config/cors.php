<?php

return [
    'paths' => ['api/*', 'auth/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:8080',
        'https://projournalmitrad.vercel.app',
        'https://*.vercel.app',
        'https://projournal.mitradacademy.mg',
        'http://projournal.mitradacademy.mg',
    ],
    'allowed_origins_patterns' => ['#^https://.*\.vercel\.app$#'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];