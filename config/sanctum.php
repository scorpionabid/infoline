<?php

use Laravel\Sanctum\Sanctum;

return [
    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    | Requests from these domains will receive stateful API authentication cookies.
    | Includes local, development, and production domains.
    */
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,localhost:8080,127.0.0.1,127.0.0.1:8000,::1,infoline.az,www.infoline.az',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    | Guards to be checked during Sanctum authentication
    */
    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Token Expiration
    |--------------------------------------------------------------------------
    | Token lifetime in minutes. Default is 7 days.
    */
    'expiration' => env('SANCTUM_TOKEN_EXPIRATION', 60 * 24 * 7), // 7 gün

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    | Prefix for tokens to enhance security and aid in scanning
    */
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'infoline_'),

    /*
    |--------------------------------------------------------------------------
    | API Prefix
    |--------------------------------------------------------------------------
    | Prefix for API routes
    */
    'prefix' => env('SANCTUM_API_PREFIX', 'api'),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    | Middleware used during authentication process
    */
    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Security Settings
    |--------------------------------------------------------------------------
    | Extra configurations to enhance API security
    */
    'security' => [
        // Limit number of tokens per user
        'max_tokens_per_user' => env('SANCTUM_MAX_TOKENS', 5),

        // Enable token rotation
        'rotate_tokens' => env('SANCTUM_ROTATE_TOKENS', true),

        // Allowed token abilities
        'allowed_abilities' => [
            'read',
            'write',
            'admin',
            'super-admin'
        ]
    ]
];