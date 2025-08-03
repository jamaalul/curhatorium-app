<?php

namespace App\Listeners;

use App\Events\XpAwarded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleXpAwarded implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(XpAwarded $event): void
    {
        // Log the XP award for analytics
        Log::info('XP awarded', [
            'user_id' => $event->user->id,
            'xp_awarded' => $event->xpAwarded,
            'activity' => $event->activity,
            'total_xp' => $event->user->total_xp,
            'progress_percentage' => $event->progress['progress_percentage']
        ]);

        // Check if user reached psychologist access threshold
        if ($event->progress['progress_percentage'] >= 100) {
            Log::info('User unlocked psychologist access', [
                'user_id' => $event->user->id,
                'total_xp' => $event->user->total_xp
            ]);
            
            // You could dispatch another event here for psychologist access unlocked
            // event(new PsychologistAccessUnlocked($event->user));
        }

        // Check if user reached daily limit
        if ($event->progress['daily_xp_gained'] >= $event->progress['max_daily_xp']) {
            Log::info('User reached daily XP limit', [
                'user_id' => $event->user->id,
                'daily_xp_gained' => $event->progress['daily_xp_gained'],
                'max_daily_xp' => $event->progress['max_daily_xp']
            ]);
        }
    }
} 