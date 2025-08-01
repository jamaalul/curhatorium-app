<?php

// Simple script to cancel expired share and talk sessions
// This can be called via cron job or web request

// Set the path to your Laravel application (since public is now in root)
$laravelPath = __DIR__ . '/';

// Include Laravel's bootstrap
require_once $laravelPath . 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $laravelPath . 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Run the specific command
    $controller = new \App\Http\Controllers\ShareAndTalkController();
    $controller->cancelExpiredWaitingSessions();
    
    echo "Success: Expired waiting sessions cancelled and tickets returned.\n";
    echo "Time: " . now()->format('Y-m-d H:i:s') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Time: " . now()->format('Y-m-d H:i:s') . "\n";
} 