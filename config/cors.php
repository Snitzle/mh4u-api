<?php

declare(strict_types=1);

return [

    'paths' => ['api/*'],

    'allowed_methods' => ['GET', 'HEAD', 'OPTIONS'],

    // The web/mobile client origins. Comma-separated env, or "*" in local dev.
    'allowed_origins' => explode(',', (string) env('CORS_ALLOWED_ORIGINS', '*')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    // Let browsers read the ETag so conditional requests / caching work.
    'exposed_headers' => ['ETag'],

    'max_age' => 0,

    'supports_credentials' => false,

];
