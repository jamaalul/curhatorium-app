<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatSession;
use Carbon\Carbon;

class CleanupExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired waiting and pending sessions, reset professional status, and refund tickets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of expired sessions...');

        // Find expired sessions (waiting or pending)
        $expiredSessions = ChatSession::whereIn('status', ['waiting', 'pending'])
            ->where(function($query) {
                $query->where('start', '<', now('Asia/Jakarta')->subMinutes(5))
                      ->orWhere('pending_end', '<', now('Asia/Jakarta'));
            })
            ->get();

        $this->info("Found {$expiredSessions->count()} expired sessions to clean up.");

        foreach ($expiredSessions as $session) {
            $this->info("Processing session: {$session->session_id} (Status: {$session->status})");

            // Mark session as cancelled
            $session->status = 'cancelled';
            $session->save();

            // Refund ticket to user
            $user = $session->user;
            if ($user) {
                if ($session->type === 'video') {
                    $ticketType = 'share_talk_psy_video';
                } else {
                    $ticketType = $session->professional->type === 'psychiatrist' ? 'share_talk_psy_chat' : 'share_talk_ranger_chat';
                }

                $ticket = $user->userTickets()
                    ->where('ticket_type', $ticketType)
                    ->where(function($q) {
                        $q->whereNull('limit_type')->orWhere('limit_type', '!=', 'unlimited');
                    })
                    ->orderByDesc('expires_at')
                    ->first();

                if ($ticket && $ticket->remaining_value !== null) {
                    $ticket->remaining_value += 1;
                    $ticket->save();
                    $this->info("  - Refunded 1 ticket to user {$user->name} for type {$ticketType}");
                } else {
                    $this->warn("  - No ticket found to refund for user {$user->name}");
                }
            }
        }

        $this->info('Cleanup completed successfully!');
        return 0;
    }
}
