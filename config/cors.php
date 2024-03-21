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

    'paths' => ['*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*', 'localhost:3000'],

    'allowed_origins_patterns' => ['localhost:3000'],

    'allowed_headers' => ['*', 'localhost:3000'],

    'exposed_headers' => ['loclahost:3000'],

    'max_age' => 0,

    'supports_credentials' => false,

];
