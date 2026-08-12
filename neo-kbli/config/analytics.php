<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Analytics Feature Flags
    |--------------------------------------------------------------------------
    |
    | This flag determines if the Analytics features (like Dashboard 
    | Statistics) should be enabled. If false, services should gracefully
    | fallback or disable computation.
    |
    */
    'enabled' => env('ANALYTICS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Dashboard Cache Settings
    |--------------------------------------------------------------------------
    |
    | Define the Time-To-Live (TTL) in seconds for various dashboard widgets.
    | This replaces magic numbers and centralizes cache configuration.
    |
    */
    'cache_ttl' => [
        'statistics' => env('ANALYTICS_CACHE_TTL_STATISTICS', 60),
        'status' => env('ANALYTICS_CACHE_TTL_STATUS', 300),
        'activities' => env('ANALYTICS_CACHE_TTL_ACTIVITIES', 30),
        'queue' => env('ANALYTICS_CACHE_TTL_QUEUE', 60),
        'reviewers' => env('ANALYTICS_CACHE_TTL_REVIEWERS', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for widget limits and default display values.
    |
    */
    'limits' => [
        'recent_activities' => 5,
        'approval_queue' => 5,
    ],
];
