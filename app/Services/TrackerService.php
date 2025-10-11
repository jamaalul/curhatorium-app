<?php

namespace App\Services;

use App\Models\Stat;
use App\Models\WeeklyStat;
use App\Models\MonthlyStat;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TrackerService
{
    /**
     * Check if user has already tracked today
     */
    public function hasTrackedToday(User $user): bool
    {
        return Stat::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->exists();
    }

    /**
     * Create a new tracker entry
     */
    public function createTrackerEntry(User $user, array $data): array
    {
        return DB::transaction(function () use ($user, $data) {
            Log::info('Creating tracker entry with data:', $data);
            
            // Get AI feedback
            $feedback = $this->getGeminiFeedback($data);
            Log::info('AI feedback generated:', ['feedback' => $feedback]);

            // Create the stat record
            $statData = [
                'user_id' => $user->id,
                'mood' => $data['mood'],
                'activity' => $data['activity'],
                'explanation' => $data['activityExplanation'] ?? null,
                'energy' => $data['energy'],
                'productivity' => $data['productivity'],
                'day' => now()->format('l'),
                'feedback' => $feedback,
            ];
            
            Log::info('Creating stat with data:', $statData);
            $stat = Stat::create($statData);
            Log::info('Stat created successfully:', ['stat_id' => $stat->id]);

            // Award XP
            $xpResult = $user->awardXp('mood_tracker');
            Log::info('XP awarded:', $xpResult);

            return [
                'stat' => $stat->toArray(),
                'xp_awarded' => $xpResult['xp_awarded'] ?? 0,
                'xp_message' => $xpResult['message'] ?? ''
            ];
        });
    }

    /**
     * Get user's stats
     */
    public function getUserStats(User $user, int $limit = 30): array
    {
        return Stat::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get specific stat
     */
    public function getStat(int $statId, User $user): ?Stat
    {
        return Stat::where('id', $statId)
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Get weekly stats
     */
    public function getWeeklyStats(User $user): array
    {
        return WeeklyStat::where('user_id', $user->id)
            ->orderBy('week_start', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get monthly stats
     */
    public function getMonthlyStats(User $user): array
    {
        return MonthlyStat::where('user_id', $user->id)
            ->orderBy('month_start', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get weekly stat detail
     */
    public function getWeeklyStatDetail(int $statId, User $user): ?array
    {
        $weeklyStat = WeeklyStat::where('id', $statId)
            ->where('user_id', $user->id)
            ->first();

        if (!$weeklyStat) {
            return null;
        }

        $stats = Stat::where('user_id', $user->id)
            ->whereBetween('created_at', [$weeklyStat->week_start, $weeklyStat->week_end])
            ->orderBy('created_at', 'asc')
            ->get();

        $analysis = $this->generateWeeklyAnalysis($stats, $weeklyStat);

        return [
            'weeklyStat' => $weeklyStat,
            'stats' => $stats,
            'analysis' => $analysis
        ];
    }

    /**
     * Get monthly stat detail
     */
    public function getMonthlyStatDetail(int $statId, User $user): ?array
    {
        $monthlyStat = MonthlyStat::where('id', $statId)
            ->where('user_id', $user->id)
            ->first();

        if (!$monthlyStat) {
            return null;
        }

        $stats = Stat::where('user_id', $user->id)
            ->whereBetween('created_at', [$monthlyStat->month_start, $monthlyStat->month_end])
            ->orderBy('created_at', 'asc')
            ->get();

        $analysis = $this->generateMonthlyAnalysis($stats, $monthlyStat);

        return [
            'monthly_stat' => $monthlyStat->toArray(),
            'stats' => $stats->toArray(),
            'analysis' => $analysis
        ];
    }

    /**
     * Get stats for dashboard
     */
    public function getStatsForDashboard(User $user): array
    {
        // Use optimized query with proper indexing and caching
        $weekStart = now()->subDays(6)->startOfDay();
        $weekEnd = now()->endOfDay();
        
        $stats = Stat::where('user_id', $user->id)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->select('mood', 'productivity', 'created_at') // Only select needed columns
            ->orderBy('created_at', 'asc')
            ->get();

        // Format data for chart
        $chartData = $stats->map(function ($stat) {
            return [
                'day' => $stat->created_at->format('D'), // Day name (Mon, Tue, etc.)
                'value' => $stat->mood,
                'productivity' => $stat->productivity
            ];
        })->toArray();

        // Calculate averages
        $averageMood = $stats->avg('mood') ?? 0;
        $averageProd = $stats->avg('productivity') ?? 0;

        return [
            'chartData' => $chartData,
            'averageMood' => number_format($averageMood, 2),
            'averageProd' => number_format($averageProd, 2)
        ];
    }

    /**
     * Get Gemini AI feedback
     */
    private function getGeminiFeedback(array $data): ?string
    {
        $prompt = $this->buildFeedbackPrompt($data);

        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

        try {
            $response = Http::post($apiUrl . '?key=' . $apiKey, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 800,
                    'temperature' => 0.7,
                ]
            ]);

            if ($response->ok()) {
                $responseData = $response->json();
                return $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;
            } else {
                Log::error('Gemini API error for tracker feedback', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Gemini API exception for tracker feedback', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Build feedback prompt for Gemini
     */
    private function buildFeedbackPrompt(array $data): string
    {
        $moodDescription = match (true) {
            $data['mood'] <= 2 => "Sangat Negatif - disarankan untuk istirahat dan cari aktivitas yang menenangkan.",
            $data['mood'] <= 4 => "Negatif - coba luangkan waktu buat refleksi atau ngobrol dengan orang yang dipercaya.",
            $data['mood'] <= 6 => "Netral - kamu cukup stabil, tapi bisa coba sesuatu yang nyenengin diri.",
            $data['mood'] <= 8 => "Positif - suasana hatimu bagus, pertahankan dan terus eksplor hal-hal positif.",
            default => "Sangat Positif - kamu lagi di titik yang baik banget, semoga bisa terus stabil ya.",
        };

        return "Berikan summary, analisis seperti analisis kecenderungan dan lainnya, dan feedback (jangan terlalu panjang. Pastikan tidak lebih dari 800 token output. gunakan bahasa santai, menenangkan, dan penuh empati, jangan terlalu formal. Jangan gunakan kata sayang seperti pacar. Anda bersifat supportif) untuk seseorang yang mengisi tracker harian dengan data berikut:\n"
            . "Mood: {$data['mood']}/10 ({$moodDescription})\n"
            . "Aktivitas utama: {$data['activity']}\n"
            . "Penjelasan aktivitas: " . ($data['activityExplanation'] ?? '-') . "\n"
            . "Energi: {$data['energy']}/10\n"
            . "Produktivitas: {$data['productivity']}/10\n"
            . "Hari: " . now()->format('l') . "\n"
            . "Jangan sebutkan bahwa kamu AI. Jawab dengan dekat yang supportif, menenangkan, dan positif.";
    }

    /**
     * Generate weekly analysis
     */
    private function generateWeeklyAnalysis($stats, $weeklyStat): array
    {
        if ($stats->isEmpty()) {
            return ['error' => 'No stats found for this week'];
        }

        // Mood Rating Analysis
        $avgMood = $weeklyStat->avg_mood;
        $moodDescription = $this->getMoodDescription($avgMood);
        $moodRecommendation = $this->getMoodRecommendation($avgMood);

        // Mood Range Analysis
        $moodRange = $stats->max('mood') - $stats->min('mood');
        $moodRangeDescription = $this->getMoodRangeDescription($moodRange);

        // Activity Analysis
        $activityAnalysis = $this->analyzeActivities($stats);

        // Productivity Analysis
        $productivityAnalysis = $this->analyzeProductivity($stats);

        // Goal and Recommendation Table
        $goalTable = $this->getGoalTable($avgMood);

        // Best Mood Activity Analysis
        $bestMoodActivity = $this->getBestMoodActivity($stats);

        return [
            'mood_rating' => [
                'average' => $avgMood,
                'description' => $moodDescription,
                'recommendation' => $moodRecommendation,
                'range' => $moodRange,
                'range_description' => $moodRangeDescription,
            ],
            'activity_analysis' => $activityAnalysis,
            'productivity_analysis' => $productivityAnalysis,
            'goal_table' => $goalTable,
            'best_mood_activity' => $bestMoodActivity,
        ];
    }

    /**
     * Generate monthly analysis
     */
    private function generateMonthlyAnalysis($stats, $monthlyStat): array
    {
        if ($stats->isEmpty()) {
            return ['error' => 'No stats found for this month'];
        }

        $avgMood = $stats->avg('mood');
        $avgEnergy = $stats->avg('energy');
        $avgProductivity = $stats->avg('productivity');

        $moodRange = $stats->max('mood') - $stats->min('mood');
        $energyRange = $stats->max('energy') - $stats->min('energy');
        $productivityRange = $stats->max('productivity') - $stats->min('productivity');

        $activities = $this->analyzeActivities($stats);
        $productivityAnalysis = $this->analyzeProductivity($stats);
        $bestMoodActivity = $this->getBestMoodActivity($stats);

        return [
            'summary' => [
                'avg_mood' => round($avgMood, 1),
                'avg_energy' => round($avgEnergy, 1),
                'avg_productivity' => round($avgProductivity, 1),
                'mood_range' => $moodRange,
                'energy_range' => $energyRange,
                'productivity_range' => $productivityRange,
            ],
            'mood_analysis' => [
                'description' => $this->getMoodDescription($avgMood),
                'recommendation' => $this->getMoodRecommendation($avgMood),
                'range_description' => $this->getMoodRangeDescription($moodRange),
            ],
            'activities' => $activities,
            'productivity' => $productivityAnalysis,
            'best_mood_activity' => $bestMoodActivity,
            'goals' => $this->getGoalTable($avgMood),
        ];
    }

    /**
     * Get mood description
     */
    private function getMoodDescription(float $avgMood): string
    {
        if ($avgMood <= 2) return 'Sangat Negatif';
        if ($avgMood <= 4) return 'Negatif';
        if ($avgMood <= 6) return 'Netral';
        if ($avgMood <= 8) return 'Positif';
        return 'Sangat Positif';
    }

    /**
     * Get mood recommendation
     */
    private function getMoodRecommendation(float $avgMood): string
    {
        if ($avgMood <= 4) return 'Meningkatkan Mood';
        if ($avgMood <= 6) return 'Menjaga Mood';
        return 'Mempertahankan Mood';
    }

    /**
     * Get mood range description
     */
    private function getMoodRangeDescription(float $range): string
    {
        if ($range <= 2) return 'Stabil';
        if ($range <= 4) return 'Sedang';
        return 'Fluktuatif';
    }

    /**
     * Analyze activities
     */
    private function analyzeActivities($stats)
    {
        $activityCounts = $stats->groupBy('activity')->map->count();
        $activityMoods = $stats->groupBy('activity')->map(function($group) {
            return [
                'avg_mood' => $group->avg('mood'),
                'avg_productivity' => $group->avg('productivity'),
                'count' => $group->count(),
                'entries' => $group
            ];
        });

        $dominantActivity = $activityCounts->sortDesc()->first();
        $dominantActivityKey = $activityCounts->sortDesc()->keys()->first();

        $analysis = [];

        // Scenario A: Dominant Activity
        if ($dominantActivity >= 3) {
            $activityData = $activityMoods[$dominantActivityKey];
            $activityNames = [
                'work' => 'Pekerjaan & Karir',
                'exercise' => 'Aktivitas Fisik',
                'social' => 'Sosialisasi',
                'hobbies' => 'Kreativitas & Hobi',
                'rest' => 'Hiburan & Santai',
                'entertainment' => 'Perawatan Diri',
                'nature' => 'Aktivitas Luar Ruangan',
                'food' => 'Rumah Tangga',
                'health' => 'Kesehatan Mental',
                'study' => 'Belajar & Produktivitas',
                'spiritual' => 'Spiritual',
                'romance' => 'Hubungan Romantis',
                'finance' => 'Finansial & Mandiri',
                'other' => 'Lainnya',
            ];

            $activityName = $activityNames[$dominantActivityKey] ?? $dominantActivityKey;
            $moodContribution = $activityData['avg_mood'] - $stats->avg('mood');
            $productivityStatus = $activityData['avg_productivity'] >= 5 ? 'baik' : 'buruk';

            $analysis['dominant'] = [
                'activity' => $activityName,
                'count' => $dominantActivity,
                'mood_contribution' => round($moodContribution, 1),
                'productivity_status' => $productivityStatus,
                'avg_mood' => round($activityData['avg_mood'], 1),
                'avg_productivity' => round($activityData['avg_productivity'], 1),
            ];
        } else {
            // Scenario B: No dominant activity
            $highestMoodActivity = $activityMoods->sortByDesc('avg_mood')->first();
            $lowestMoodActivity = $activityMoods->sortBy('avg_mood')->first();

            $activityNames = [
                'work' => 'Pekerjaan & Karir',
                'exercise' => 'Aktivitas Fisik',
                'social' => 'Sosialisasi',
                'hobbies' => 'Kreativitas & Hobi',
                'rest' => 'Hiburan & Santai',
                'entertainment' => 'Perawatan Diri',
                'nature' => 'Aktivitas Luar Ruangan',
                'food' => 'Rumah Tangga',
                'health' => 'Kesehatan Mental',
                'study' => 'Belajar & Produktivitas',
                'spiritual' => 'Spiritual',
                'romance' => 'Hubungan Romantis',
                'finance' => 'Finansial & Mandiri',
                'other' => 'Lainnya',
            ];

            $analysis['varied'] = [
                'highest' => [
                    'activity' => $activityNames[array_keys($activityMoods->toArray())[0]] ?? 'Unknown',
                    'avg_mood' => round($highestMoodActivity['avg_mood'], 1),
                    'avg_productivity' => round($highestMoodActivity['avg_productivity'], 1),
                ],
                'lowest' => [
                    'activity' => $activityNames[array_keys($activityMoods->toArray())[count($activityMoods)-1]] ?? 'Unknown',
                    'avg_mood' => round($lowestMoodActivity['avg_mood'], 1),
                    'avg_productivity' => round($lowestMoodActivity['avg_productivity'], 1),
                ]
            ];
        }

        return $analysis;
    }

    /**
     * Analyze productivity
     */
    private function analyzeProductivity($stats)
    {
        $avgProductivity = $stats->avg('productivity');

        // Find most and least productive days
        $dailyProductivity = $stats->groupBy(function($stat) {
            return $stat->created_at->format('Y-m-d');
        })->map(function($dayStats) {
            return [
                'avg_productivity' => $dayStats->avg('productivity'),
                'date' => $dayStats->first()->created_at,
                'activities' => $dayStats->pluck('activity')->toArray(),
                'mood' => $dayStats->avg('mood'),
            ];
        });

        $mostProductiveDay = $dailyProductivity->sortByDesc('avg_productivity')->first();
        $leastProductiveDay = $dailyProductivity->sortBy('avg_productivity')->first();

        return [
            'average' => round($avgProductivity, 1),
            'most_productive' => [
                'date' => $mostProductiveDay['date'],
                'productivity' => round($mostProductiveDay['avg_productivity'], 1),
                'activities' => $mostProductiveDay['activities'],
                'mood' => round($mostProductiveDay['mood'], 1),
            ],
            'least_productive' => [
                'date' => $leastProductiveDay['date'],
                'productivity' => round($leastProductiveDay['avg_productivity'], 1),
                'activities' => $leastProductiveDay['activities'],
                'mood' => round($leastProductiveDay['mood'], 1),
            ]
        ];
    }

    /**
     * Get goal table based on average mood
     */
    private function getGoalTable(float $avgMood): array
    {
        if ($avgMood <= 4.0) {
            return [
                'pattern' => 'Low Mood Week',
                'goal' => 'Increase Emotional Support & Energy',
                'theoretical_backing' => 'Behavioral Activation (BA), Social Support Theory, CBT',
                'how_it_helps' => 'Builds structure, increases perceived support, enhances self-awareness'
            ];
        } elseif ($avgMood <= 6.0) {
            return [
                'pattern' => 'Fluctuating Mood',
                'goal' => 'Create Routine, Reduce Variability',
                'theoretical_backing' => 'Chronobiology, Habit Formation, Affective Activation Theory',
                'how_it_helps' => 'Stabilizes circadian rhythm, enhances positive affect, improves emotional regulation'
            ];
        } else {
            return [
                'pattern' => 'Good/Stable Mood',
                'goal' => 'Reinforce Success, Growth Focus',
                'theoretical_backing' => 'Self-Monitoring, Positive Psychology, Self-Determination Theory',
                'how_it_helps' => 'Reinforces adaptive habits, increases purpose and engagement, boosts social bonding'
            ];
        }
    }

    /**
     * Get best mood activity
     */
    private function getBestMoodActivity($stats)
    {
        if ($stats->isEmpty()) {
            return null;
        }

        // Find the stat with the highest mood
        $bestMoodStat = $stats->sortByDesc('mood')->first();

        $activityNames = [
            'work' => 'Pekerjaan & Karir',
            'exercise' => 'Aktivitas Fisik',
            'social' => 'Sosialisasi',
            'hobbies' => 'Kreativitas & Hobi',
            'rest' => 'Hiburan & Santai',
            'entertainment' => 'Perawatan Diri',
            'nature' => 'Aktivitas Luar Ruangan',
            'food' => 'Rumah Tangga',
            'health' => 'Kesehatan Mental',
            'study' => 'Belajar & Produktivitas',
            'spiritual' => 'Spiritual',
            'romance' => 'Hubungan Romantis',
            'finance' => 'Finansial & Mandiri',
            'other' => 'Lainnya',
        ];

        return [
            'activity' => $activityNames[$bestMoodStat->activity] ?? $bestMoodStat->activity,
            'mood_score' => $bestMoodStat->mood,
            'productivity' => $bestMoodStat->productivity,
            'date' => $bestMoodStat->created_at,
            'explanation' => $bestMoodStat->explanation,
        ];
    }
} 