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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'fonnte' => [
        'token' => env('FONNTE_TOKEN'),
    ],
    'fcm' => [
        'api_key' => env('VITE_FCM_API_KEY'),
        'auth_domain' => env('VITE_FCM_AUTH_DOMAIN'),
        'project_id' => env('VITE_FCM_PROJECT_ID'),
        'storage_bucket' => env('VITE_FCM_STORAGE_BUCKET'),
        'messaging_sender_id' => env('VITE_FCM_MESSAGING_SENDER_ID'),
        'app_id' => env('VITE_FCM_APP_ID'),
        'measurement_id' => env('VITE_FCM_MEASUREMENT_ID'),
    ],
];
