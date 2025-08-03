<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\DailyXpLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class XpRepository
{
    /**
     * Get daily XP gained by user for a specific date
     */
    public function getDailyXpGained(User $user, Carbon $date = null): int
    {
        $date = $date ?? Carbon::today();
        
        return DailyXpLog::where('user_id', $user->id)
            ->whereDate('created_at', $date)
            ->sum('xp_gained');
    }

    /**
     * Get XP history for a user within a date range
     */
    public function getXpHistory(User $user, int $days = 30): Collection
    {
        return DailyXpLog::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get XP breakdown by activity for a user within a date range
     */
    public function getXpBreakdownByActivity(User $user, int $days = 30): array
    {
        return DailyXpLog::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->selectRaw('activity, COUNT(*) as count, SUM(xp_gained) as total_xp')
            ->groupBy('activity')
            ->get()
            ->keyBy('activity')
            ->toArray();
    }

    /**
     * Get users who have reached psychologist access threshold
     */
    public function getUsersWithPsychologistAccess(): Collection
    {
        $targetXp = config('xp.targets.psychologist_access');
        
        return User::where('total_xp', '>=', $targetXp)->get();
    }

    /**
     * Get users who are close to reaching psychologist access (within 10%)
     */
    public function getUsersNearPsychologistAccess(): Collection
    {
        $targetXp = config('xp.targets.psychologist_access');
        $threshold = $targetXp * 0.9; // 90% of target
        
        return User::where('total_xp', '>=', $threshold)
            ->where('total_xp', '<', $targetXp)
            ->get();
    }

    /**
     * Get top XP earners
     */
    public function getTopXpEarners(int $limit = 10): Collection
    {
        return User::orderBy('total_xp', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get daily XP statistics for all users
     */
    public function getDailyXpStatistics(Carbon $date = null): array
    {
        $date = $date ?? Carbon::today();
        
        $stats = DailyXpLog::whereDate('created_at', $date)
            ->selectRaw('
                COUNT(DISTINCT user_id) as active_users,
                SUM(xp_gained) as total_xp_awarded,
                AVG(xp_gained) as avg_xp_per_user,
                COUNT(*) as total_activities
            ')
            ->first();

        return [
            'date' => $date->format('Y-m-d'),
            'active_users' => $stats->active_users ?? 0,
            'total_xp_awarded' => $stats->total_xp_awarded ?? 0,
            'avg_xp_per_user' => round($stats->avg_xp_per_user ?? 0, 2),
            'total_activities' => $stats->total_activities ?? 0,
        ];
    }

    /**
     * Get activity popularity statistics
     */
    public function getActivityPopularity(int $days = 7): array
    {
        return DailyXpLog::where('created_at', '>=', Carbon::now()->subDays($days))
            ->selectRaw('activity, COUNT(*) as usage_count, SUM(xp_gained) as total_xp')
            ->groupBy('activity')
            ->orderBy('usage_count', 'desc')
            ->get()
            ->toArray();
    }
} 