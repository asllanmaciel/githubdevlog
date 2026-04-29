<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mercado_pago' => [
        'environment' => env('MERCADO_PAGO_ENVIRONMENT', 'sandbox'),
        'access_token' => env('MERCADO_PAGO_ENVIRONMENT', 'sandbox') === 'production'
            ? env('MERCADO_PAGO_PRODUCTION_ACCESS_TOKEN', env('MERCADO_PAGO_ACCESS_TOKEN'))
            : env('MERCADO_PAGO_SANDBOX_ACCESS_TOKEN', env('MERCADO_PAGO_ACCESS_TOKEN')),
        'public_key' => env('MERCADO_PAGO_ENVIRONMENT', 'sandbox') === 'production'
            ? env('MERCADO_PAGO_PRODUCTION_PUBLIC_KEY', env('MERCADO_PAGO_PUBLIC_KEY'))
            : env('MERCADO_PAGO_SANDBOX_PUBLIC_KEY', env('MERCADO_PAGO_PUBLIC_KEY')),
        'webhook_secret' => env('MERCADO_PAGO_WEBHOOK_SECRET'),
        'webhook_tolerance_seconds' => env('MERCADO_PAGO_WEBHOOK_TOLERANCE_SECONDS', 900),
    ],

    'github_app' => [
        'name' => env('GITHUB_APP_NAME', 'GitHub DevLog AI'),
        'app_id' => env('GITHUB_APP_ID'),
        'client_id' => env('GITHUB_APP_CLIENT_ID'),
        'client_secret' => env('GITHUB_APP_CLIENT_SECRET'),
        'webhook_secret' => env('GITHUB_APP_WEBHOOK_SECRET'),
        'private_key_path' => env('GITHUB_APP_PRIVATE_KEY_PATH'),
        'callback_url' => env('GITHUB_APP_CALLBACK_URL'),
        'setup_url' => env('GITHUB_APP_SETUP_URL'),
        'webhook_url' => env('GITHUB_APP_WEBHOOK_URL'),
    ],
];
