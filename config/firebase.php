<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Cloud Messaging (FCM) notifications
    |
    */

    'server_key' => env('FIREBASE_SERVER_KEY'), // Legacy API (deprecated)
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
    'app_id' => env('FIREBASE_APP_ID'),
    
    // V1 API Configuration
    'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH', storage_path('app/firebase-service-account.json')),
    
    /*
    |--------------------------------------------------------------------------
    | FCM URL
    |--------------------------------------------------------------------------
    |
    | The Firebase Cloud Messaging API endpoint
    |
    */
    
    'fcm_url' => 'https://fcm.googleapis.com/fcm/send',
    
    /*
    |--------------------------------------------------------------------------
    | Default Notification Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for notifications
    |
    */
    
    'default_sound' => 'default',
    'default_badge' => 1,
    'default_priority' => 'high',
];