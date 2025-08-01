<?php

// Comprehensive cron script for Curhatorium App
// This handles all the frequent scheduled tasks

// Set the path to your Laravel application (since public is now in root)
$laravelPath = __DIR__ . '/';

// Include Laravel's bootstrap
require_once $laravelPath . 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $laravelPath . 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = [];

try {
    // 1. Cancel expired share and talk sessions (every minute)
    $shareTalkCommand = new \App\Console\Commands\CancelExpiredShareAndTalkSessions();
    $shareTalkCommand->handle();
    $results[] = "✓ Share and Talk: Expired sessions cancelled";
    
    // 2. Cleanup expired sessions (every minute)
    $sessionsCommand = new \App\Console\Commands\CleanupExpiredSessions();
    $sessionsCommand->handle();
    $results[] = "✓ Sessions: Expired sessions cleaned up";
    
    // 3. SGD mark done (every 5 minutes - we'll run it every time for simplicity)
    $sgdCommand = new \App\Console\Commands\MarkSgdGroupsDone();
    $sgdCommand->handle();
    $results[] = "✓ SGD: Marked done";
    
    // 4. Cleanup expired tickets (daily - we'll run it every time for safety)
    $ticketsCommand = new \App\Console\Commands\CleanupExpiredTickets();
    $ticketsCommand->handle();
    $results[] = "✓ Tickets: Expired tickets cleaned up";
    
    echo "Success: All scheduled tasks completed.\n";
    echo "Time: " . now()->format('Y-m-d H:i:s') . "\n";
    echo "Tasks:\n";
    foreach ($results as $result) {
        echo "  " . $result . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Time: " . now()->format('Y-m-d H:i:s') . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 