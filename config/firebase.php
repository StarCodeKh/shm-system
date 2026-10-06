<?php

// Relative key paths are resolved from the project root, so they work for web, artisan and queue workers
$keyPath = fn ($path) => ($path && $path[0] !== '/') ? base_path($path) : $path;

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Path to the Firebase Admin SDK private key JSON file. This is used to
    | authenticate your Laravel backend with Firebase services like
    | Authentication, Cloud Messaging, Firestore, etc.
    |
    */

    'credentials' => $keyPath(env('FIREBASE_CREDENTIALS')),

    /*
    |--------------------------------------------------------------------------
    | Firebase Database URL
    |--------------------------------------------------------------------------
    |
    | If you're using the Firebase Realtime Database, you may specify the
    | database URL here. If left null, the SDK may attempt to figure it out.
    |
    */

    'database_url' => env('FIREBASE_DATABASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Used in various services like Firestore and Cloud Messaging.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),

];