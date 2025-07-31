<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyXpLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class XpService
{
    // XP Constants based on the image
    const TOTAL_XP_FOR_PSYCHOLOGIST = 10000; // Monthly target
    const MAX_DAILY_XP_PAID_MEMBERSHIP = 333;
    const MAX_DAILY_XP_CALM_STARTER = 222;

    // XP Values for activities
    const XP_VALUES = [
        'mental_test' => [
            'free' => 10,
            'subscription' => 10
        ],
        'share_talk_ranger' => [
            'free' => 5,
            'subscription' => 25
        ],
        'share_talk_psychiatrist' => [
            'free' => 0,
            'subscription' => 80
        ],
        'mission_easy' => [
            'free' => 6,
            'subscription' => 6
        ],
        'mission_medium' => [
            'free' => 8,
            'subscription' => 8
        ],
        'mission_hard' => [
            'free' => 16,
            'subscription' => 16
        ],
        'mentai_chatbot' => [
            'free' => 10,
            'subscription' => 10
        ],
        'deep_cards' => [
            'free' => 5,
            'subscription' => 5
        ],
        'support_group' => [
            'free' => 27,
            'subscription' => 28
        ],
        'mood_tracker' => [
            'free' => 15,
            'subscription' => 25
        ]
    ];

    /**
     * Award XP to a user for a specific activity
     */
    public function awardXp(User $user, string $activity, int $quantity = 1): array
    {
        // Check if user has already reached daily limit
        $dailyXpGained = $this->getDailyXpGained($user);
        $maxDailyXp = $this->getMaxDailyXp($user);
        
        if ($dailyXpGained >= $maxDailyXp) {
            return [
                'success' => false,
                'message' => 'Daily XP limit reached',
                'xp_awarded' => 0,
                'daily_xp_gained' => $dailyXpGained,
                'max_daily_xp' => $maxDailyXp
            ];
        }

        // Get XP value for the activity
        $xpValue = $this->getXpValue($user, $activity);
        if ($xpValue === null) {
            return [
                'success' => false,
                'message' => 'Invalid activity',
                'xp_awarded' => 0
            ];
        }

        $totalXpToAward = $xpValue * $quantity;
        
        // Check if awarding this XP would exceed daily limit
        if ($dailyXpGained + $totalXpToAward > $maxDailyXp) {
            $totalXpToAward = $maxDailyXp - $dailyXpGained;
        }

        if ($totalXpToAward <= 0) {
            return [
                'success' => false,
                'message' => 'Daily XP limit reached',
                'xp_awarded' => 0,
                'daily_xp_gained' => $dailyXpGained,
                'max_daily_xp' => $maxDailyXp
            ];
        }

        // Award XP in a transaction
        DB::transaction(function () use ($user, $totalXpToAward, $activity) {
            // Update user's total XP
            $user->increment('total_xp', $totalXpToAward);
            
            // Log daily XP
            $this->logDailyXp($user, $totalXpToAward, $activity);
        });

        return [
            'success' => true,
            'message' => 'XP awarded successfully',
            'xp_awarded' => $totalXpToAward,
            'new_total_xp' => $user->fresh()->total_xp,
            'daily_xp_gained' => $this->getDailyXpGained($user),
            'max_daily_xp' => $maxDailyXp
        ];
    }

    /**
     * Get XP value for an activity based on user's membership
     */
    public function getXpValue(User $user, string $activity): ?int
    {
        if (!isset(self::XP_VALUES[$activity])) {
            return null;
        }

        $membershipType = $this->getUserMembershipType($user);
        return self::XP_VALUES[$activity][$membershipType];
    }

    /**
     * Get user's membership type (free or subscription)
     */
    public function getUserMembershipType(User $user): string
    {
        // Check if user has Growth Path, Blossom, or Inner Peace membership
        $hasPremiumMembership = $user->activeMemberships()
            ->whereHas('membership', function($query) {
                $query->whereIn('name', ['Growth Path', 'Blossom', 'Inner Peace']);
            })
            ->exists();

        return $hasPremiumMembership ? 'subscription' : 'free';
    }

    /**
     * Get maximum daily XP for user based on membership
     */
    public function getMaxDailyXp(User $user): int
    {
        $membershipType = $this->getUserMembershipType($user);
        return $membershipType === 'subscription' 
            ? self::MAX_DAILY_XP_PAID_MEMBERSHIP 
            : self::MAX_DAILY_XP_CALM_STARTER;
    }

    /**
     * Get daily XP gained by user today
     */
    public function getDailyXpGained(User $user): int
    {
        return DailyXpLog::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('xp_gained');
    }

    /**
     * Log daily XP gain
     */
    private function logDailyXp(User $user, int $xpGained, string $activity): void
    {
        DailyXpLog::create([
            'user_id' => $user->id,
            'xp_gained' => $xpGained,
            'activity' => $activity,
            'created_at' => now()
        ]);
    }

    /**
     * Get XP progress towards psychologist access
     */
    public function getXpProgress(User $user): array
    {
        $totalXp = $user->total_xp;
        $progress = ($totalXp / self::TOTAL_XP_FOR_PSYCHOLOGIST) * 100;
        
        return [
            'current_xp' => $totalXp,
            'target_xp' => self::TOTAL_XP_FOR_PSYCHOLOGIST,
            'progress_percentage' => min(100, $progress),
            'xp_remaining' => max(0, self::TOTAL_XP_FOR_PSYCHOLOGIST - $totalXp)
        ];
    }

    /**
     * Check if user can access psychologist
     */
    public function canAccessPsychologist(User $user): bool
    {
        return $user->total_xp >= self::TOTAL_XP_FOR_PSYCHOLOGIST;
    }

    /**
     * Get XP breakdown for all activities
     */
    public function getXpBreakdown(User $user): array
    {
        $membershipType = $this->getUserMembershipType($user);
        $breakdown = [];

        foreach (self::XP_VALUES as $activity => $values) {
            $breakdown[$activity] = [
                'xp_value' => $values[$membershipType],
                'membership_type' => $membershipType
            ];
        }

        return $breakdown;
    }

    /**
     * Get daily XP summary
     */
    public function getDailyXpSummary(User $user): array
    {
        $dailyXpGained = $this->getDailyXpGained($user);
        $maxDailyXp = $this->getMaxDailyXp($user);
        
        return [
            'daily_xp_gained' => $dailyXpGained,
            'max_daily_xp' => $maxDailyXp,
            'remaining_daily_xp' => max(0, $maxDailyXp - $dailyXpGained),
            'daily_progress_percentage' => ($dailyXpGained / $maxDailyXp) * 100
        ];
    }
} 