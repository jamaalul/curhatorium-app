<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Optimization Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for database optimization
    | including caching strategies, query optimization, and performance
    | monitoring settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache duration settings for different types of data
    |
    */
    'cache' => [
        'durations' => [
            'short' => env('DB_CACHE_SHORT', 300),      // 5 minutes
            'medium' => env('DB_CACHE_MEDIUM', 1800),   // 30 minutes
            'long' => env('DB_CACHE_LONG', 3600),       // 1 hour
            'daily' => env('DB_CACHE_DAILY', 86400),    // 24 hours
        ],

        'prefixes' => [
            'user_stats' => 'user_stats_',
            'user_tickets' => 'user_tickets_',
            'user_memberships' => 'user_memberships_',
            'missions' => 'missions_',
            'cards' => 'cards_',
            'memberships' => 'memberships_',
            'leaderboard' => 'leaderboard_',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for query optimization and performance monitoring
    |
    */
    'queries' => [
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 100), // milliseconds
        'enable_query_logging' => env('DB_ENABLE_QUERY_LOGGING', false),
        'max_query_time' => env('DB_MAX_QUERY_TIME', 5000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Index Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for database indexes and optimization
    |
    */
    'indexes' => [
        'auto_optimize' => env('DB_AUTO_OPTIMIZE', true),
        'optimize_frequency' => env('DB_OPTIMIZE_FREQUENCY', 'daily'), // daily, weekly, monthly
        'tables_to_optimize' => [
            'users',
            'stats',
            'user_tickets',
            'user_memberships',
            'chat_sessions',
            'messages',
            'mission_completions',
            'chatbot_sessions',
            'chatbot_messages',
            'daily_xp_logs',
            'weekly_stats',
            'monthly_stats',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Settings for monitoring database performance
    |
    */
    'monitoring' => [
        'enabled' => env('DB_MONITORING_ENABLED', true),
        'log_slow_queries' => env('DB_LOG_SLOW_QUERIES', true),
        'log_cache_hits' => env('DB_LOG_CACHE_HITS', false),
        'alert_threshold' => env('DB_ALERT_THRESHOLD', 1000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Pooling
    |--------------------------------------------------------------------------
    |
    | Settings for database connection pooling
    |
    */
    'connections' => [
        'max_connections' => env('DB_MAX_CONNECTIONS', 100),
        'min_connections' => env('DB_MIN_CONNECTIONS', 5),
        'connection_timeout' => env('DB_CONNECTION_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Schedule
    |--------------------------------------------------------------------------
    |
    | Schedule for database maintenance tasks
    |
    */
    'maintenance' => [
        'optimize_tables' => [
            'frequency' => 'daily',
            'time' => '02:00', // 2 AM
        ],
        'clear_expired_cache' => [
            'frequency' => 'hourly',
        ],
        'update_statistics' => [
            'frequency' => 'weekly',
            'day' => 'sunday',
            'time' => '03:00', // 3 AM
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for database backup optimization
    |
    */
    'backup' => [
        'optimize_before_backup' => env('DB_OPTIMIZE_BEFORE_BACKUP', true),
        'exclude_tables' => [
            'cache',
            'sessions',
            'failed_jobs',
        ],
    ],
]; 