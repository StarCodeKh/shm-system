<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    // Browsers on other sites may call the API only from these origins: APP_URL plus a comma-separated
    // CORS_ALLOWED_ORIGINS list. The web app (same origin) and the mobile app (native) need no entry.
    'allowed_origins' => array_values(array_filter(array_unique(array_map('trim', array_merge(
        [rtrim((string) env('APP_URL', ''), '/')],
        explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))
    ))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // The API signs in with Bearer tokens, not cookies, so cross-site credentials are not needed
    'supports_credentials' => false,

];
