<?php

// Test script to verify cron functionality
// Access this via browser to test: https://yourdomain.com/test-cron.php

// Set the path to your Laravel application (since public is now in root)
$laravelPath = __DIR__ . '/';

// Include Laravel's bootstrap
require_once $laravelPath . 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $laravelPath . 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Cron Test Results</h2>";
echo "<p><strong>Time:</strong> " . now()->format('Y-m-d H:i:s') . "</p>";

try {
    // Test 1: Check if we can access the database
    $sessionCount = \App\Models\ChatSession::whereIn('status', ['waiting', 'pending'])->count();
    echo "<p><strong>✓ Database Connection:</strong> Working</p>";
    echo "<p><strong>Active Sessions:</strong> {$sessionCount} waiting/pending sessions</p>";
    
    // Test 2: Check if we can run the share and talk command
    $shareTalkCommand = new \App\Console\Commands\CancelExpiredShareAndTalkSessions();
    $shareTalkCommand->handle();
    echo "<p><strong>✓ Share and Talk Command:</strong> Executed successfully</p>";
    
    // Test 3: Check if we can run the sessions cleanup command
    $sessionsCommand = new \App\Console\Commands\CleanupExpiredSessions();
    $sessionsCommand->handle();
    echo "<p><strong>✓ Sessions Cleanup:</strong> Executed successfully</p>";
    
    // Test 4: Check if we can run the SGD command
    $sgdCommand = new \App\Console\Commands\MarkSgdGroupsDone();
    $sgdCommand->handle();
    echo "<p><strong>✓ SGD Command:</strong> Executed successfully</p>";
    
    // Test 5: Check if we can run the tickets cleanup command
    $ticketsCommand = new \App\Console\Commands\CleanupExpiredTickets();
    $ticketsCommand->handle();
    echo "<p><strong>✓ Tickets Cleanup:</strong> Executed successfully</p>";
    
    echo "<p><strong style='color: green;'>✓ All tests passed! Cron functionality is working.</strong></p>";
    
} catch (Exception $e) {
    echo "<p><strong style='color: red;'>✗ Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} 