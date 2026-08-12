<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Workflow Queue Settings
    |--------------------------------------------------------------------------
    |
    | Define the retry logic and backoff strategy for workflow event 
    | subscribers and notifications.
    |
    */
    'queue' => [
        'tries' => (int) env('WORKFLOW_QUEUE_TRIES', 3),
        'backoff' => [60, 180, 300], // Detik
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable workflow notification features.
    |
    */
    'features' => [
        'email_notifications' => env('WORKFLOW_ENABLE_EMAILS', true),
        'queue_enabled' => env('WORKFLOW_QUEUE_ENABLED', true),
    ]
];
