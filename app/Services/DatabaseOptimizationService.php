<?php

namespace App\Services;

use App\Models\User;
use App\Models\Stat;
use App\Models\Mission;
use App\Models\Card;
use App\Models\Membership;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DatabaseOptimizationService
{
    /**
     * Cache duration constants
     */
    private const CACHE_DURATIONS = [
        'short' => 300,      // 5 minutes
        'medium' => 1800,    // 30 minutes
        'long' => 3600,      // 1 hour
        'daily' => 86400,    // 24 hours
    ];

    /**
     * Get optimized user stats for dashboard
     */
    public function getOptimizedUserStats(User $user): array
    {
        $cacheKey = "user_stats_{$user->id}_" . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['medium'], function () use ($user) {
            // Use optimized query with proper indexing
            $stats = Stat::where('user_id', $user->id)
                ->whereBetween('created_at', [
                    now()->subDays(6)->startOfDay(),
                    now()->endOfDay()
                ])
                ->select('mood', 'productivity', 'created_at')
                ->orderBy('created_at', 'asc')
                ->get();

            $chartData = $stats->map(function ($stat) {
                return [
                    'day' => $stat->created_at->format('D'),
                    'value' => $stat->mood,
                    'productivity' => $stat->productivity
                ];
            })->toArray();

            $averageMood = $stats->avg('mood') ?? 0;
            $averageProd = $stats->avg('productivity') ?? 0;

            return [
                'chartData' => $chartData,
                'averageMood' => number_format($averageMood, 2),
                'averageProd' => number_format($averageProd, 2)
            ];
        });
    }

    /**
     * Get optimized mission data
     */
    public function getOptimizedMissions(): array
    {
        $cacheKey = 'missions_active_' . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['long'], function () {
            return Mission::where('is_active', true)
                ->where(function($query) {
                    $query->where('start_date', '<=', now())
                          ->where('end_date', '>=', now())
                          ->orWhereNull('start_date')
                          ->orWhereNull('end_date');
                })
                ->select('id', 'name', 'description', 'type', 'xp_reward')
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get optimized cards data
     */
    public function getOptimizedCards(string $category = null, string $difficulty = null): array
    {
        $cacheKey = "cards_{$category}_{$difficulty}_" . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['long'], function () use ($category, $difficulty) {
            $query = Card::where('is_active', true);
            
            if ($category) {
                $query->where('category', $category);
            }
            
            if ($difficulty) {
                $query->where('difficulty', $difficulty);
            }
            
            return $query->select('id', 'title', 'content', 'category', 'difficulty')
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get optimized memberships data
     */
    public function getOptimizedMemberships(): array
    {
        $cacheKey = 'memberships_all_' . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['daily'], function () {
            return Membership::with(['membershipTickets' => function($query) {
                $query->select('membership_id', 'ticket_type', 'limit_type', 'limit_value');
            }])
            ->select('id', 'name', 'description', 'price', 'duration', 'duration_type')
            ->orderBy('price')
            ->get()
            ->toArray();
        });
    }

    /**
     * Get optimized user tickets with caching
     */
    public function getOptimizedUserTickets(User $user, string $ticketType = null): array
    {
        $cacheKey = "user_tickets_{$user->id}_{$ticketType}_" . now()->format('Y-m-d-H');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['short'], function () use ($user, $ticketType) {
            $query = DB::table('user_tickets')
                ->where('user_id', $user->id)
                ->where('expires_at', '>', now());
            
            if ($ticketType) {
                $query->where('ticket_type', $ticketType);
            }
            
            return $query->select('id', 'ticket_type', 'limit_type', 'limit_value', 'remaining_value', 'expires_at')
                ->orderBy('expires_at', 'asc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get optimized user memberships with caching
     */
    public function getOptimizedUserMemberships(User $user): array
    {
        $cacheKey = "user_memberships_{$user->id}_" . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['medium'], function () use ($user) {
            return DB::table('user_memberships')
                ->join('memberships', 'user_memberships.membership_id', '=', 'memberships.id')
                ->where('user_memberships.user_id', $user->id)
                ->where('user_memberships.expires_at', '>', now())
                ->where('user_memberships.status', 'active')
                ->select(
                    'user_memberships.id',
                    'user_memberships.expires_at',
                    'memberships.name',
                    'memberships.description',
                    'memberships.price'
                )
                ->orderBy('user_memberships.created_at', 'desc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get optimized leaderboard data
     */
    public function getOptimizedLeaderboard(int $limit = 10): array
    {
        $cacheKey = "leaderboard_{$limit}_" . now()->format('Y-m-d-H');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['medium'], function () use ($limit) {
            return User::select('id', 'username', 'total_xp', 'profile_picture')
                ->orderBy('total_xp', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get optimized mission completions for user
     */
    public function getOptimizedMissionCompletions(User $user, int $limit = 30): array
    {
        $cacheKey = "mission_completions_{$user->id}_{$limit}_" . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['medium'], function () use ($user, $limit) {
            return DB::table('mission_completions')
                ->join('missions', 'mission_completions.mission_id', '=', 'missions.id')
                ->where('mission_completions.user_id', $user->id)
                ->select(
                    'mission_completions.id',
                    'mission_completions.completed_at',
                    'missions.name',
                    'missions.description',
                    'missions.type'
                )
                ->orderBy('mission_completions.completed_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get optimized chat sessions for user
     */
    public function getOptimizedChatSessions(User $user, int $limit = 20): array
    {
        $cacheKey = "chat_sessions_{$user->id}_{$limit}_" . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['short'], function () use ($user, $limit) {
            return DB::table('chat_sessions')
                ->join('professionals', 'chat_sessions.professional_id', '=', 'professionals.id')
                ->where('chat_sessions.user_id', $user->id)
                ->select(
                    'chat_sessions.id',
                    'chat_sessions.session_id',
                    'chat_sessions.start',
                    'chat_sessions.end',
                    'chat_sessions.status',
                    'professionals.name as professional_name',
                    'professionals.specialization'
                )
                ->orderBy('chat_sessions.created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get optimized weekly stats for user
     */
    public function getOptimizedWeeklyStats(User $user): array
    {
        $cacheKey = "weekly_stats_{$user->id}_" . now()->format('Y-W');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['long'], function () use ($user) {
            return DB::table('weekly_stats')
                ->where('user_id', $user->id)
                ->orderBy('week_start', 'desc')
                ->limit(12) // Last 12 weeks
                ->get()
                ->toArray();
        });
    }

    /**
     * Get optimized monthly stats for user
     */
    public function getOptimizedMonthlyStats(User $user): array
    {
        $cacheKey = "monthly_stats_{$user->id}_" . now()->format('Y-m');
        
        return Cache::remember($cacheKey, self::CACHE_DURATIONS['long'], function () use ($user) {
            return DB::table('monthly_stats')
                ->where('user_id', $user->id)
                ->orderBy('month_start', 'desc')
                ->limit(12) // Last 12 months
                ->get()
                ->toArray();
        });
    }

    /**
     * Clear user-specific cache
     */
    public function clearUserCache(User $user): void
    {
        $patterns = [
            "user_stats_{$user->id}_*",
            "user_tickets_{$user->id}_*",
            "user_memberships_{$user->id}_*",
            "mission_completions_{$user->id}_*",
            "chat_sessions_{$user->id}_*",
            "weekly_stats_{$user->id}_*",
            "monthly_stats_{$user->id}_*"
        ];

        foreach ($patterns as $pattern) {
            $this->clearCacheByPattern($pattern);
        }

        Log::info('User cache cleared', ['user_id' => $user->id]);
    }

    /**
     * Clear cache by pattern
     */
    private function clearCacheByPattern(string $pattern): void
    {
        // This is a simplified version. In production, you might want to use Redis SCAN
        // or implement a more sophisticated cache clearing mechanism
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                // For Redis, you can use SCAN to find and delete keys
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys($pattern);
                foreach ($keys as $key) {
                    $redis->del($key);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear cache by pattern', [
                'pattern' => $pattern,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get database query statistics
     */
    public function getQueryStatistics(): array
    {
        $stats = [
            'total_queries' => DB::getQueryLog() ? count(DB::getQueryLog()) : 0,
            'slow_queries' => 0,
            'cache_hits' => 0,
            'cache_misses' => 0
        ];

        // Analyze query log for slow queries
        if (DB::getQueryLog()) {
            foreach (DB::getQueryLog() as $query) {
                if (isset($query['time']) && $query['time'] > 100) { // Queries taking more than 100ms
                    $stats['slow_queries']++;
                }
            }
        }

        return $stats;
    }

    /**
     * Optimize database tables
     */
    public function optimizeTables(): array
    {
        $tables = [
            'users', 'stats', 'user_tickets', 'user_memberships',
            'chat_sessions', 'messages', 'mission_completions',
            'chatbot_sessions', 'chatbot_messages', 'daily_xp_logs',
            'weekly_stats', 'monthly_stats'
        ];

        $results = [];
        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE {$table}");
                $results[$table] = 'optimized';
            } catch (\Exception $e) {
                $results[$table] = 'failed: ' . $e->getMessage();
            }
        }

        Log::info('Database tables optimized', $results);
        return $results;
    }

    /**
     * Get cache statistics
     */
    public function getCacheStatistics(): array
    {
        $stats = [
            'total_keys' => 0,
            'memory_usage' => 0,
            'hit_rate' => 0
        ];

        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $info = $redis->info();
                
                $stats['total_keys'] = $info['db0']['keys'] ?? 0;
                $stats['memory_usage'] = $info['used_memory_human'] ?? '0B';
                $stats['hit_rate'] = $this->calculateHitRate($info);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get cache statistics', ['error' => $e->getMessage()]);
        }

        return $stats;
    }

    /**
     * Calculate cache hit rate
     */
    private function calculateHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;
        
        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    /**
     * Warm up frequently accessed cache
     */
    public function warmUpCache(): void
    {
        try {
            // Warm up missions cache
            $this->getOptimizedMissions();
            
            // Warm up memberships cache
            $this->getOptimizedMemberships();
            
            // Warm up leaderboard cache
            $this->getOptimizedLeaderboard();
            
            Log::info('Cache warmed up successfully');
        } catch (\Exception $e) {
            Log::error('Failed to warm up cache', ['error' => $e->getMessage()]);
        }
    }
} 