<?php

// Relative key paths are resolved from the project root, so they work for web, artisan and queue workers
$keyPath = fn ($path) => ($path && $path[0] !== '/') ? base_path($path) : $path;

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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    // Firebase Cloud Messaging (push notifications), see App\Services\PushNotificationService
    'fcm' => [
        'credentials' => $keyPath(env('SERVICE_ACCOUNT_KEY_PATH')),
        'scope' => env('FCM_SCOPE', 'https://www.googleapis.com/auth/firebase.messaging'),
        'sender_id' => env('FCM_SENDER_ID'),
    ],

    // Mobile sign-in codes (App\Services\OTPService). No SMS provider is connected yet, so a test
    // server can return the code in the API response (OTP_EXPOSE_CODE=true); production keeps it false.
    'otp' => [
        'expose_code' => (bool) env('OTP_EXPOSE_CODE', false),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
