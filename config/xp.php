<?php

return [
    /*
    |--------------------------------------------------------------------------
    | XP System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all configuration values for the XP (Experience Points)
    | system, including targets, daily limits, and activity rewards.
    |
    */

    // XP Targets
    'targets' => [
        'psychologist_access' => env('XP_PSYCHOLOGIST_TARGET', 10000),
    ],

    // Daily XP Limits
    'daily_limits' => [
        'free' => env('XP_DAILY_LIMIT_FREE', 222),
        'subscription' => env('XP_DAILY_LIMIT_SUBSCRIPTION', 333),
    ],

    // XP Values for activities by membership type
    'activities' => [
        'mental_test' => [
            'free' => 10,
            'subscription' => 10,
            'description' => 'Mental Health Test',
        ],
        'share_talk_ranger' => [
            'free' => 5,
            'subscription' => 25,
            'description' => 'Share and Talk (Ranger)',
        ],
        'share_talk_psychiatrist' => [
            'free' => 0,
            'subscription' => 80,
            'description' => 'Share and Talk (Psychiatrist)',
        ],
        'mission_easy' => [
            'free' => 6,
            'subscription' => 6,
            'description' => 'Mission (Easy)',
        ],
        'mission_medium' => [
            'free' => 8,
            'subscription' => 8,
            'description' => 'Mission (Medium)',
        ],
        'mission_hard' => [
            'free' => 16,
            'subscription' => 16,
            'description' => 'Mission (Hard)',
        ],
        'mentai_chatbot' => [
            'free' => 10,
            'subscription' => 10,
            'description' => 'Ment-AI Chatbot',
        ],
        'deep_cards' => [
            'free' => 5,
            'subscription' => 5,
            'description' => 'Deep Cards',
        ],
        'support_group' => [
            'free' => 27,
            'subscription' => 28,
            'description' => 'Support Group Discussion',
        ],
        'mood_tracker' => [
            'free' => 15,
            'subscription' => 25,
            'description' => 'Mood Tracker',
        ],
    ],

    // Membership types that qualify for subscription benefits
    'subscription_memberships' => [
        'Growth Path',
        'Blossom', 
        'Inner Peace',
    ],
]; 