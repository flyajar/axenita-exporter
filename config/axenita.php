<?php

return [
    'base_url' => env('AXENITA_BASE_URL', 'https://aesgen01.axenita.ch'),

    // If you want these stable defaults:
    'ui_context' => env('AXENITA_UI_CONTEXT', 'DEFAULT'),
    'language'   => env('AXENITA_LANGUAGE', 'fr'),
    'workspace'  => env('AXENITA_WORKSPACE', 'null'),

    // This one looks like a client-generated id. Keep it stable per app instance if possible.
    'notification_client_id' => env('AXENITA_NOTIFICATION_CLIENT_ID'),

    // Cookie "namespace" suffix you have (the UUID-looking part in cookie names)
    'cookie_namespace' => env('AXENITA_COOKIE_NAMESPACE'),

    // If you want to seed cookies from env (optional; usually you’ll obtain them via login flow)
    'seed_cookies' => [
        'csrf_cookie' => env('AXENITA_CSRF_COOKIE'),
        'language_cookie' => env('AXENITA_LANGUAGE_COOKIE', 'FRENCH'),
    ],

    // If you want to seed the header token too (often changes; best stored/refreshed)
    'csrf_header_token' => env('AXENITA_CSRF_HEADER_TOKEN'),
];
