<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketService
{
    /**
     * Check if user has valid tickets for a feature
     */
    public function hasValidTickets(User $user, string $ticketType): bool
    {
        return $user->userTickets()
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', Carbon::now())
            ->where('remaining_value', '>', 0)
            ->exists();
    }

    /**
     * Consume a ticket for a user
     */
    public function consumeTicket(User $user, string $ticketType, int $amount = 1): bool
    {
        return DB::transaction(function () use ($user, $ticketType, $amount) {
            $ticket = $user->userTickets()
                ->where('ticket_type', $ticketType)
                ->where('expires_at', '>', Carbon::now())
                ->where('remaining_value', '>', 0)
                ->orderBy('expires_at')
                ->first();

            if (!$ticket) {
                return false;
            }

            $ticket->remaining_value -= $amount;
            $ticket->save();

            // Clean up expired tickets
            $this->cleanupExpiredTickets();

            return true;
        });
    }

    /**
     * Get user's remaining tickets for a type
     */
    public function getRemainingTickets(User $user, string $ticketType): int
    {
        return $user->userTickets()
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', Carbon::now())
            ->sum('remaining_value');
    }

    /**
     * Refund a ticket to user
     */
    public function refundTicket(User $user, string $ticketType, int $amount = 1): bool
    {
        return DB::transaction(function () use ($user, $ticketType, $amount) {
            $ticket = $user->userTickets()
                ->where('ticket_type', $ticketType)
                ->where(function($q) {
                    $q->whereNull('limit_type')->orWhere('limit_type', '!=', 'unlimited');
                })
                ->orderByDesc('expires_at')
                ->first();

            if (!$ticket || $ticket->remaining_value === null) {
                Log::warning("No ticket found to refund for user {$user->id}, ticket type: {$ticketType}");
                return false;
            }

            $ticket->remaining_value += $amount;
            $ticket->save();
            
            Log::info("Ticket refunded for user {$user->id}, ticket type: {$ticketType}, refund amount: {$amount}");
            return true;
        });
    }

    /**
     * Clean up expired tickets
     */
    private function cleanupExpiredTickets(): void
    {
        UserTicket::where('expires_at', '<', Carbon::now())
            ->orWhere('remaining_value', '<=', 0)
            ->delete();
    }

    /**
     * Get ticket summary for user
     */
    public function getTicketSummary(User $user): array
    {
        $tickets = $user->userTickets()
            ->where('expires_at', '>', Carbon::now())
            ->where('remaining_value', '>', 0)
            ->get()
            ->groupBy('ticket_type')
            ->map(function ($tickets) {
                return [
                    'total_remaining' => $tickets->sum('remaining_value'),
                    'tickets' => $tickets->map(function ($ticket) {
                        return [
                            'id' => $ticket->id,
                            'remaining_value' => $ticket->remaining_value,
                            'limit_type' => $ticket->limit_type,
                            'expires_at' => $ticket->expires_at,
                        ];
                    })
                ];
            });

        return $tickets->toArray();
    }
} 