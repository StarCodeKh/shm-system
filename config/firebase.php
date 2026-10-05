<?php

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

    'credentials' => env('FIREBASE_CREDENTIALS'),

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

    'project_id' => env('shm-app-ccb29'),

];