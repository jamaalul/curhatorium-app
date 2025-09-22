<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\UserMembership;
use App\Models\UserTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MembershipService
{
    /**
     * Get all available memberships
     */
    public function getAllMemberships(): array
    {
        return Membership::orderBy('price')->get()->toArray();
    }

    /**
     * Get membership by ID
     */
    public function getMembership(int $id): ?array
    {
        $membership = Membership::find($id);
        return $membership ? $membership->toArray() : null;
    }

    /**
     * Purchase a membership for a user
     */
    public function purchaseMembership(User $user, int $membershipId): array
    {
        return DB::transaction(function () use ($user, $membershipId) {
            $membership = Membership::findOrFail($membershipId);
            
            Log::info('User purchasing membership', [
                'user_id' => $user->id,
                'membership_id' => $membershipId,
                'membership_name' => $membership->name
            ]);

            // Create user membership record
            $userMembership = UserMembership::create([
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'purchased_at' => now(),
                'expires_at' => $this->calculateExpiryDate($membership),
                'status' => 'active'
            ]);

            // Create tickets based on membership
            $tickets = $this->createMembershipTickets($user, $membership);

            Log::info('Membership purchased successfully', [
                'user_membership_id' => $userMembership->id,
                'tickets_created' => count($tickets)
            ]);

            return [
                'user_membership' => $userMembership->toArray(),
                'tickets' => $tickets,
                'membership' => $membership->toArray()
            ];
        });
    }

    /**
     * Get user's active memberships
     */
    public function getUserActiveMemberships(User $user): array
    {
        return UserMembership::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->where('status', 'active')
            ->with(['membership' => function($query) {
                $query->select('id', 'name', 'description', 'price');
            }])
            ->select('id', 'user_id', 'membership_id', 'expires_at', 'status')
            ->get()
            ->toArray();
    }

    /**
     * Get user's membership history
     */
    public function getUserMembershipHistory(User $user): array
    {
        return UserMembership::where('user_id', $user->id)
            ->with('membership')
            ->orderBy('purchased_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Check if user has active membership
     */
    public function hasActiveMembership(User $user): bool
    {
        return UserMembership::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Check if user has specific active membership
     */
    public function hasActiveMembershipByName(User $user, string $membershipName): bool
    {
        return UserMembership::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->where('status', 'active')
            ->whereHas('membership', function($query) use ($membershipName) {
                $query->where('name', $membershipName);
            })
            ->exists();
    }

    /**
     * Get user's primary membership (most recent active)
     */
    public function getPrimaryMembership(User $user): ?array
    {
        $membership = UserMembership::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->where('status', 'active')
            ->with('membership')
            ->orderBy('purchased_at', 'desc')
            ->first();

        return $membership ? $membership->toArray() : null;
    }

    /**
     * Get user's active tickets
     */
    public function getUserActiveTickets(User $user): array
    {
        return UserTicket::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->select('id', 'ticket_type', 'limit_type', 'limit_value', 'remaining_value', 'expires_at')
            ->orderBy('expires_at', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get user's tickets by type
     */
    public function getUserTicketsByType(User $user, string $ticketType): array
    {
        return UserTicket::where('user_id', $user->id)
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', now())
            ->orderBy('expires_at', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Check if user has valid ticket for feature
     */
    public function hasValidTicket(User $user, string $ticketType): bool
    {
        return UserTicket::where('user_id', $user->id)
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Consume a ticket
     */
    public function consumeTicket(User $user, string $ticketType, int $quantity = 1): array
    {
        return DB::transaction(function () use ($user, $ticketType, $quantity) {
            $tickets = UserTicket::where('user_id', $user->id)
                ->where('ticket_type', $ticketType)
                ->where('expires_at', '>', now())
                ->where('remaining_value', '>', 0)
                ->orderBy('expires_at', 'asc')
                ->get();

            if ($tickets->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No valid tickets found for this feature.'
                ];
            }

            $consumed = 0;
            $consumedTickets = [];

            foreach ($tickets as $ticket) {
                if ($consumed >= $quantity) break;

                $canConsume = min($ticket->remaining_value, $quantity - $consumed);
                $ticket->remaining_value -= $canConsume;
                $ticket->save();

                $consumed += $canConsume;
                $consumedTickets[] = $ticket;

                // Delete ticket if fully consumed
                if ($ticket->remaining_value <= 0) {
                    $ticket->delete();
                }
            }

            Log::info('Tickets consumed', [
                'user_id' => $user->id,
                'ticket_type' => $ticketType,
                'quantity_requested' => $quantity,
                'quantity_consumed' => $consumed,
                'tickets_used' => count($consumedTickets)
            ]);

            return [
                'success' => true,
                'consumed' => $consumed,
                'tickets' => $consumedTickets
            ];
        });
    }

    /**
     * Calculate expiry date for membership
     */
    private function calculateExpiryDate(Membership $membership): Carbon
    {
        $duration = $membership->duration;
        $durationType = $membership->duration_type;

        return match ($durationType) {
            'days' => now()->addDays($duration),
            'weeks' => now()->addWeeks($duration),
            'months' => now()->addMonths($duration),
            'years' => now()->addYears($duration),
            default => now()->addDays($duration),
        };
    }

    /**
     * Create tickets based on membership
     */
    private function createMembershipTickets(User $user, Membership $membership): array
    {
        $tickets = [];
        $membershipTickets = $membership->membershipTickets;

        foreach ($membershipTickets as $membershipTicket) {
            $ticketData = [
                'user_id' => $user->id,
                'ticket_type' => $membershipTicket->ticket_type,
                'limit_type' => $membershipTicket->limit_type,
                'limit_value' => $membershipTicket->limit_value,
                'remaining_value' => $membershipTicket->limit_value,
                'expires_at' => $this->calculateTicketExpiry($membership, $membershipTicket),
            ];

            $ticket = UserTicket::create($ticketData);
            $tickets[] = $ticket->toArray();

            Log::info('Ticket created for membership', [
                'user_id' => $user->id,
                'ticket_type' => $membershipTicket->ticket_type,
                'limit_type' => $membershipTicket->limit_type,
                'limit_value' => $membershipTicket->limit_value,
                'expires_at' => $ticket->expires_at
            ]);
        }

        return $tickets;
    }

    /**
     * Calculate ticket expiry date
     */
    private function calculateTicketExpiry(Membership $membership, $membershipTicket): Carbon
    {
        // If membership has specific duration, use that
        if ($membership->duration && $membership->duration_type) {
            return $this->calculateExpiryDate($membership);
        }

        // Otherwise, use default expiry (30 days from now)
        return now()->addDays(30);
    }

    /**
     * Get membership statistics
     */
    public function getMembershipStats(): array
    {
        $totalMemberships = UserMembership::count();
        $activeMemberships = UserMembership::where('expires_at', '>', now())
            ->where('status', 'active')
            ->count();
        $expiredMemberships = UserMembership::where('expires_at', '<=', now())
            ->count();

        $membershipBreakdown = Membership::withCount(['userMemberships' => function($query) {
            $query->where('expires_at', '>', now())
                  ->where('status', 'active');
        }])->get()->map(function($membership) {
            return [
                'name' => $membership->name,
                'active_count' => $membership->user_memberships_count
            ];
        })->toArray();

        return [
            'total_memberships' => $totalMemberships,
            'active_memberships' => $activeMemberships,
            'expired_memberships' => $expiredMemberships,
            'membership_breakdown' => $membershipBreakdown
        ];
    }

    /**
     * Clean up expired memberships
     */
    public function cleanupExpiredMemberships(): int
    {
        $expiredCount = UserMembership::where('expires_at', '<=', now())
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        Log::info('Expired memberships cleaned up', ['count' => $expiredCount]);

        return $expiredCount;
    }
} 