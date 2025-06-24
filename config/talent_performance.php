<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Talent System Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for optimizing the talent request system performance
    |
    */

    'pagination' => [
        'talents_per_page' => env('TALENTS_PER_PAGE', 12),
        'requests_per_page' => env('REQUESTS_PER_PAGE', 15),
        'max_per_page' => env('MAX_PER_PAGE', 50),
    ],

    'caching' => [
        'talent_availability_ttl' => env('TALENT_AVAILABILITY_CACHE_TTL', 600), // 10 minutes
        'analytics_ttl' => env('ANALYTICS_CACHE_TTL', 1800), // 30 minutes
        'top_talents_ttl' => env('TOP_TALENTS_CACHE_TTL', 600), // 10 minutes
        'skill_analytics_ttl' => env('SKILL_ANALYTICS_CACHE_TTL', 3600), // 1 hour
        'dashboard_ttl' => env('DASHBOARD_CACHE_TTL', 1800), // 30 minutes
        'manage_talents_ttl' => env('MANAGE_TALENTS_CACHE_TTL', 300), // 5 minutes
    ],

    'search' => [
        'max_search_results' => env('MAX_SEARCH_RESULTS', 50),
        'chunk_size' => env('SEARCH_CHUNK_SIZE', 100),
        'default_limit' => env('DEFAULT_SEARCH_LIMIT', 20),
    ],

    'optimization' => [
        'eager_load_relationships' => env('EAGER_LOAD_RELATIONSHIPS', true),
        'enable_query_caching' => env('ENABLE_QUERY_CACHING', true),
        'enable_availability_cache' => env('ENABLE_AVAILABILITY_CACHE', true),
        'batch_availability_checks' => env('BATCH_AVAILABILITY_CHECKS', true),
    ],

    'ui' => [
        'show_metrics_by_default' => env('SHOW_METRICS_BY_DEFAULT', false),
        'enable_infinite_scroll' => env('ENABLE_INFINITE_SCROLL', false),
        'async_loading' => env('ASYNC_LOADING', true),
    ]
];
