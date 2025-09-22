<?php

// Test script to check session status and activation
// Access via browser: https://yourdomain.com/test-session.php

// Set the path to your Laravel application
$laravelPath = __DIR__ . '/';

// Include Laravel's bootstrap
require_once $laravelPath . 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $laravelPath . 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Session Status Test</h2>";

try {
    // Get the most recent session
    $recentSession = \App\Models\ChatSession::whereIn('status', ['waiting', 'pending', 'active'])
        ->latest()
        ->first();
    
    if ($recentSession) {
        echo "<h3>Recent Session Details:</h3>";
        echo "<ul>";
        echo "<li><strong>Session ID:</strong> " . $recentSession->session_id . "</li>";
        echo "<li><strong>Status:</strong> " . $recentSession->status . "</li>";
        echo "<li><strong>Type:</strong> " . ($recentSession->type ?? 'NULL') . "</li>";
        echo "<li><strong>Created:</strong> " . $recentSession->created_at->format('Y-m-d H:i:s') . "</li>";
        echo "<li><strong>Start:</strong> " . $recentSession->start->format('Y-m-d H:i:s') . "</li>";
        echo "<li><strong>End:</strong> " . $recentSession->end->format('Y-m-d H:i:s') . "</li>";
        echo "<li><strong>Pending End:</strong> " . ($recentSession->pending_end ? $recentSession->pending_end->format('Y-m-d H:i:s') : 'NULL') . "</li>";
        echo "</ul>";
        
        // Test the activation endpoint
        echo "<h3>Test Activation:</h3>";
        echo "<p><a href='/share-and-talk/activate-session/" . $recentSession->session_id . "' target='_blank'>Click here to test activation link</a></p>";
        
        // Test manual activation
        echo "<h3>Test Manual Activation:</h3>";
        echo "<button onclick='testManualActivation(\"" . $recentSession->session_id . "\")'>Test Manual Activation</button>";
        echo "<div id='activation-result'></div>";
        
    } else {
        echo "<p>No recent sessions found.</p>";
    }
    
} catch (Exception $e) {
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
}

?>

<script>
async function testManualActivation(sessionId) {
    try {
        const response = await fetch(`/api/share-and-talk/manual-activate/${sessionId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        document.getElementById('activation-result').innerHTML = 
            `<p><strong>Result:</strong> ${JSON.stringify(result)}</p>`;
        
        if (response.ok) {
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    } catch (error) {
        document.getElementById('activation-result').innerHTML = 
            `<p><strong>Error:</strong> ${error.message}</p>`;
    }
}
</script> 