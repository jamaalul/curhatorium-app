<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyXpLog;
use App\Events\XpAwarded;
use App\Repositories\XpRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XpService
{
    public function __construct(
        private XpRepository $xpRepository
    ) {}

    /**
     * Award XP to a user for a specific activity
     */
    public function awardXp(User $user, string $activity, int $quantity = 1): array
    {
        try {
            // Validate activity
            if (!$this->isValidActivity($activity)) {
                return $this->createErrorResponse('Invalid activity', 0);
            }

            // Check daily limit
            $dailyLimitCheck = $this->checkDailyLimit($user, $activity, $quantity);
            if (!$dailyLimitCheck['can_award']) {
                return $dailyLimitCheck;
            }

            $xpToAward = $dailyLimitCheck['xp_to_award'];

            // Award XP in a transaction
            DB::transaction(function () use ($user, $xpToAward, $activity) {
                $this->updateUserXp($user, $xpToAward);
                $this->logDailyXp($user, $xpToAward, $activity);
            });

            // Dispatch event for XP award
            $progress = $this->getXpProgress($user);
            event(new XpAwarded($user, $xpToAward, $activity, $progress));

            return $this->createSuccessResponse($user, $xpToAward);

        } catch (\Exception $e) {
            Log::error('XP award failed', [
                'user_id' => $user->id,
                'activity' => $activity,
                'error' => $e->getMessage()
            ]);

            return $this->createErrorResponse('Failed to award XP', 0);
        }
    }

    /**
     * Get XP value for an activity based on user's membership
     */
    public function getXpValue(User $user, string $activity): ?int
    {
        if (!$this->isValidActivity($activity)) {
            return null;
        }

        $membershipType = $this->getUserMembershipType($user);
        return config("xp.activities.{$activity}.{$membershipType}");
    }

    /**
     * Get user's membership type (free or subscription)
     */
    public function getUserMembershipType(User $user): string
    {
        $hasPremiumMembership = $user->activeMemberships()
            ->whereHas('membership', function($query) {
                $query->whereIn('name', config('xp.subscription_memberships'));
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
        return config("xp.daily_limits.{$membershipType}");
    }

    /**
     * Get daily XP gained by user today
     */
    public function getDailyXpGained(User $user): int
    {
        return $this->xpRepository->getDailyXpGained($user);
    }

    /**
     * Get XP progress towards psychologist access
     */
    public function getXpProgress(User $user): array
    {
        $totalXp = $user->total_xp;
        $targetXp = config('xp.targets.psychologist_access');
        $progress = ($totalXp / $targetXp) * 100;
        
        return [
            'current_xp' => $totalXp,
            'target_xp' => $targetXp,
            'progress_percentage' => min(100, $progress),
            'xp_remaining' => max(0, $targetXp - $totalXp)
        ];
    }

    /**
     * Check if user can access psychologist
     */
    public function canAccessPsychologist(User $user): bool
    {
        return $user->total_xp >= config('xp.targets.psychologist_access');
    }

    /**
     * Get XP breakdown for all activities
     */
    public function getXpBreakdown(User $user): array
    {
        $membershipType = $this->getUserMembershipType($user);
        $breakdown = [];

        foreach (config('xp.activities') as $activity => $config) {
            $breakdown[$activity] = [
                'xp_value' => $config[$membershipType],
                'description' => $config['description'],
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

    /**
     * Get XP history for a user
     */
    public function getXpHistory(User $user, int $days = 30): array
    {
        $logs = $this->xpRepository->getXpHistory($user, $days);
        
        return $logs->groupBy(function ($log) {
            return $log->created_at->format('Y-m-d');
        })
        ->map(function ($dayLogs) {
            return [
                'total_xp' => $dayLogs->sum('xp_gained'),
                'activities' => $dayLogs->groupBy('activity')
                    ->map(function ($activityLogs) {
                        return [
                            'count' => $activityLogs->count(),
                            'total_xp' => $activityLogs->sum('xp_gained')
                        ];
                    })
            ];
        })
        ->toArray();
    }

    /**
     * Check if activity is valid
     */
    private function isValidActivity(string $activity): bool
    {
        return config("xp.activities.{$activity}") !== null;
    }

    /**
     * Check daily limit and calculate XP to award
     */
    private function checkDailyLimit(User $user, string $activity, int $quantity): array
    {
        $dailyXpGained = $this->getDailyXpGained($user);
        $maxDailyXp = $this->getMaxDailyXp($user);
        
        if ($dailyXpGained >= $maxDailyXp) {
            return $this->createErrorResponse('Daily XP limit reached', 0, $dailyXpGained, $maxDailyXp);
        }

        $xpValue = $this->getXpValue($user, $activity);
        $totalXpToAward = $xpValue * $quantity;
        
        // Check if awarding this XP would exceed daily limit
        if ($dailyXpGained + $totalXpToAward > $maxDailyXp) {
            $totalXpToAward = $maxDailyXp - $dailyXpGained;
        }

        if ($totalXpToAward <= 0) {
            return $this->createErrorResponse('Daily XP limit reached', 0, $dailyXpGained, $maxDailyXp);
        }

        return [
            'can_award' => true,
            'xp_to_award' => $totalXpToAward
        ];
    }

    /**
     * Update user's total XP
     */
    private function updateUserXp(User $user, int $xpGained): void
    {
        $user->increment('total_xp', $xpGained);
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
     * Create success response
     */
    private function createSuccessResponse(User $user, int $xpAwarded): array
    {
        return [
            'success' => true,
            'message' => 'XP awarded successfully',
            'xp_awarded' => $xpAwarded,
            'new_total_xp' => $user->fresh()->total_xp,
            'daily_xp_gained' => $this->getDailyXpGained($user),
            'max_daily_xp' => $this->getMaxDailyXp($user)
        ];
    }

    /**
     * Create error response
     */
    private function createErrorResponse(string $message, int $xpAwarded, int $dailyXpGained = 0, int $maxDailyXp = 0): array
    {
        $response = [
            'success' => false,
            'message' => $message,
            'xp_awarded' => $xpAwarded
        ];

        if ($dailyXpGained > 0 || $maxDailyXp > 0) {
            $response['daily_xp_gained'] = $dailyXpGained;
            $response['max_daily_xp'] = $maxDailyXp;
        }

        return $response;
    }
} 