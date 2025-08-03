<?php

namespace App\Services;

use App\Models\Mission;
use App\Models\MissionCompletion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MissionService
{
    /**
     * Get all available missions
     */
    public function getAllMissions(): array
    {
        return Mission::orderBy('created_at', 'desc')->get()->toArray();
    }

    /**
     * Get mission by ID
     */
    public function getMission(int $id): ?array
    {
        $mission = Mission::find($id);
        return $mission ? $mission->toArray() : null;
    }

    /**
     * Get today's missions
     */
    public function getTodayMissions(): array
    {
        return Mission::where('is_active', true)
            ->where(function($query) {
                $query->where('start_date', '<=', now())
                      ->where('end_date', '>=', now())
                      ->orWhereNull('start_date')
                      ->orWhereNull('end_date');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get user's mission completions
     */
    public function getUserMissionCompletions(User $user, int $limit = 30): array
    {
        return MissionCompletion::where('user_id', $user->id)
            ->with('mission')
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Check if user has completed a mission today
     */
    public function hasCompletedMissionToday(User $user, int $missionId): bool
    {
        return MissionCompletion::where('user_id', $user->id)
            ->where('mission_id', $missionId)
            ->whereDate('completed_at', now()->toDateString())
            ->exists();
    }

    /**
     * Complete a mission for a user
     */
    public function completeMission(User $user, int $missionId): array
    {
        return DB::transaction(function () use ($user, $missionId) {
            // Check if mission exists and is active
            $mission = Mission::where('id', $missionId)
                ->where('is_active', true)
                ->first();

            if (!$mission) {
                return [
                    'success' => false,
                    'message' => 'Mission not found or inactive.'
                ];
            }

            // Check if user already completed this mission today
            if ($this->hasCompletedMissionToday($user, $missionId)) {
                return [
                    'success' => false,
                    'message' => 'You have already completed this mission today.'
                ];
            }

            // Create mission completion record
            $completion = MissionCompletion::create([
                'user_id' => $user->id,
                'mission_id' => $mission->id,
                'completed_at' => now()
            ]);

            // Award XP based on mission type
            $xpResult = $user->awardXp($this->getMissionXpActivity($mission->type));

            Log::info('Mission completed', [
                'user_id' => $user->id,
                'mission_id' => $missionId,
                'mission_name' => $mission->name,
                'xp_awarded' => $xpResult['xp_awarded'] ?? 0
            ]);

            return [
                'success' => true,
                'completion' => $completion->toArray(),
                'mission' => $mission->toArray(),
                'xp_awarded' => $xpResult['xp_awarded'] ?? 0,
                'xp_message' => $xpResult['message'] ?? ''
            ];
        });
    }

    /**
     * Get user's mission progress
     */
    public function getUserMissionProgress(User $user): array
    {
        $todayMissions = $this->getTodayMissions();
        $completedToday = MissionCompletion::where('user_id', $user->id)
            ->whereDate('completed_at', now()->toDateString())
            ->pluck('mission_id')
            ->toArray();

        $progress = [];
        foreach ($todayMissions as $mission) {
            $progress[] = [
                'mission' => $mission,
                'completed' => in_array($mission['id'], $completedToday),
                'completed_at' => $this->getCompletionTime($user->id, $mission['id'])
            ];
        }

        return [
            'missions' => $progress,
            'total_missions' => count($todayMissions),
            'completed_missions' => count($completedToday),
            'completion_rate' => count($todayMissions) > 0 ? 
                round((count($completedToday) / count($todayMissions)) * 100, 1) : 0
        ];
    }

    /**
     * Get user's mission statistics
     */
    public function getUserMissionStats(User $user): array
    {
        $totalCompletions = MissionCompletion::where('user_id', $user->id)->count();
        $thisWeekCompletions = MissionCompletion::where('user_id', $user->id)
            ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $thisMonthCompletions = MissionCompletion::where('user_id', $user->id)
            ->whereBetween('completed_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $missionTypes = MissionCompletion::where('user_id', $user->id)
            ->with('mission')
            ->get()
            ->groupBy('mission.type')
            ->map(function($completions) {
                return $completions->count();
            })
            ->toArray();

        return [
            'total_completions' => $totalCompletions,
            'this_week_completions' => $thisWeekCompletions,
            'this_month_completions' => $thisMonthCompletions,
            'mission_types' => $missionTypes
        ];
    }

    /**
     * Get mission completion time
     */
    private function getCompletionTime(int $userId, int $missionId): ?string
    {
        $completion = MissionCompletion::where('user_id', $userId)
            ->where('mission_id', $missionId)
            ->whereDate('completed_at', now()->toDateString())
            ->first();

        return $completion ? $completion->completed_at->format('H:i') : null;
    }

    /**
     * Get XP activity name for mission type
     */
    private function getMissionXpActivity(string $missionType): string
    {
        return match ($missionType) {
            'daily' => 'daily_mission',
            'weekly' => 'weekly_mission',
            'special' => 'special_mission',
            'challenge' => 'challenge_mission',
            default => 'mission_completion'
        };
    }

    /**
     * Get mission streak for user
     */
    public function getUserMissionStreak(User $user): array
    {
        $completions = MissionCompletion::where('user_id', $user->id)
            ->orderBy('completed_at', 'desc')
            ->get()
            ->groupBy(function($completion) {
                return $completion->completed_at->format('Y-m-d');
            });

        $streak = 0;
        $currentDate = now()->startOfDay();

        foreach ($completions as $date => $dayCompletions) {
            $completionDate = Carbon::parse($date)->startOfDay();
            
            if ($currentDate->diffInDays($completionDate) <= 1 && count($dayCompletions) > 0) {
                $streak++;
                $currentDate = $completionDate;
            } else {
                break;
            }
        }

        return [
            'current_streak' => $streak,
            'longest_streak' => $this->getLongestStreak($user->id),
            'last_completion_date' => $this->getLastCompletionDate($user->id)
        ];
    }

    /**
     * Get longest streak for user
     */
    private function getLongestStreak(int $userId): int
    {
        $completions = MissionCompletion::where('user_id', $userId)
            ->orderBy('completed_at', 'asc')
            ->get()
            ->groupBy(function($completion) {
                return $completion->completed_at->format('Y-m-d');
            });

        $longestStreak = 0;
        $currentStreak = 0;
        $previousDate = null;

        foreach ($completions as $date => $dayCompletions) {
            $completionDate = Carbon::parse($date);
            
            if ($previousDate && $completionDate->diffInDays($previousDate) == 1) {
                $currentStreak++;
            } else {
                $currentStreak = 1;
            }

            $longestStreak = max($longestStreak, $currentStreak);
            $previousDate = $completionDate;
        }

        return $longestStreak;
    }

    /**
     * Get last completion date for user
     */
    private function getLastCompletionDate(int $userId): ?string
    {
        $lastCompletion = MissionCompletion::where('user_id', $userId)
            ->orderBy('completed_at', 'desc')
            ->first();

        return $lastCompletion ? $lastCompletion->completed_at->format('Y-m-d') : null;
    }

    /**
     * Get mission recommendations for user
     */
    public function getMissionRecommendations(User $user): array
    {
        $userStats = $this->getUserMissionStats($user);
        $completedTypes = array_keys($userStats['mission_types']);
        
        $recommendations = Mission::where('is_active', true)
            ->whereNotIn('type', $completedTypes)
            ->orWhere(function($query) use ($completedTypes) {
                if (empty($completedTypes)) {
                    $query->where('type', 'daily');
                }
            })
            ->limit(3)
            ->get()
            ->toArray();

        return $recommendations;
    }

    /**
     * Get mission leaderboard
     */
    public function getMissionLeaderboard(int $limit = 10): array
    {
        return User::withCount(['missionCompletions' => function($query) {
            $query->whereBetween('completed_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }])
        ->orderBy('mission_completions_count', 'desc')
        ->limit($limit)
        ->get()
        ->map(function($user) {
            return [
                'user_id' => $user->id,
                'username' => $user->username,
                'completions_count' => $user->mission_completions_count,
                'avatar' => $user->profile_picture
            ];
        })
        ->toArray();
    }
} 