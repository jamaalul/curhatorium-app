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
    public function getStat(int $statId, User $user): ?array
    {
        $stat = Stat::where('id', $statId)
            ->where('user_id', $user->id)
            ->first();

        return $stat ? $stat->toArray() : null;
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
            'weekly_stat' => $weeklyStat->toArray(),
            'stats' => $stats->toArray(),
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
        return match (true) {
            $avgMood <= 2 => "Sangat Negatif",
            $avgMood <= 4 => "Negatif",
            $avgMood <= 6 => "Netral",
            $avgMood <= 8 => "Positif",
            default => "Sangat Positif",
        };
    }

    /**
     * Get mood recommendation
     */
    private function getMoodRecommendation(float $avgMood): string
    {
        return match (true) {
            $avgMood <= 2 => "Istirahat dan cari aktivitas yang menenangkan",
            $avgMood <= 4 => "Luangkan waktu untuk refleksi atau ngobrol dengan orang yang dipercaya",
            $avgMood <= 6 => "Coba sesuatu yang menyenangkan diri",
            $avgMood <= 8 => "Pertahankan dan terus eksplor hal-hal positif",
            default => "Semoga bisa terus stabil",
        };
    }

    /**
     * Get mood range description
     */
    private function getMoodRangeDescription(float $range): string
    {
        return match (true) {
            $range <= 2 => "Stabil",
            $range <= 4 => "Agak Fluktuatif",
            $range <= 6 => "Fluktuatif",
            default => "Sangat Fluktuatif",
        };
    }

    /**
     * Analyze activities
     */
    private function analyzeActivities($stats): array
    {
        $activityCounts = $stats->groupBy('activity')->map->count();
        $totalDays = $stats->count();

        $activityAnalysis = [];
        foreach ($activityCounts as $activity => $count) {
            $percentage = round(($count / $totalDays) * 100, 1);
            $avgMood = $stats->where('activity', $activity)->avg('mood');
            $avgEnergy = $stats->where('activity', $activity)->avg('energy');
            $avgProductivity = $stats->where('activity', $activity)->avg('productivity');

            $activityAnalysis[] = [
                'activity' => $activity,
                'count' => $count,
                'percentage' => $percentage,
                'avg_mood' => round($avgMood, 1),
                'avg_energy' => round($avgEnergy, 1),
                'avg_productivity' => round($avgProductivity, 1),
            ];
        }

        // Sort by count descending
        usort($activityAnalysis, fn($a, $b) => $b['count'] - $a['count']);

        return $activityAnalysis;
    }

    /**
     * Analyze productivity
     */
    private function analyzeProductivity($stats): array
    {
        $avgProductivity = $stats->avg('productivity');
        $productivityRange = $stats->max('productivity') - $stats->min('productivity');

        $highProductivityDays = $stats->where('productivity', '>=', 8)->count();
        $lowProductivityDays = $stats->where('productivity', '<=', 4)->count();
        $totalDays = $stats->count();

        return [
            'avg_productivity' => round($avgProductivity, 1),
            'productivity_range' => $productivityRange,
            'high_productivity_days' => $highProductivityDays,
            'low_productivity_days' => $lowProductivityDays,
            'high_productivity_percentage' => round(($highProductivityDays / $totalDays) * 100, 1),
            'low_productivity_percentage' => round(($lowProductivityDays / $totalDays) * 100, 1),
        ];
    }

    /**
     * Get goal table based on average mood
     */
    private function getGoalTable(float $avgMood): array
    {
        return match (true) {
            $avgMood <= 2 => [
                ['goal' => 'Istirahat yang cukup', 'target' => '7-8 jam tidur'],
                ['goal' => 'Aktivitas menenangkan', 'target' => 'Meditasi 10 menit'],
                ['goal' => 'Koneksi sosial', 'target' => 'Ngobrol dengan teman'],
            ],
            $avgMood <= 4 => [
                ['goal' => 'Refleksi diri', 'target' => 'Journaling 15 menit'],
                ['goal' => 'Aktivitas fisik ringan', 'target' => 'Jalan-jalan 30 menit'],
                ['goal' => 'Hobi yang menyenangkan', 'target' => '1 jam per hari'],
            ],
            $avgMood <= 6 => [
                ['goal' => 'Eksplorasi aktivitas baru', 'target' => 'Coba 1 hal baru'],
                ['goal' => 'Koneksi sosial', 'target' => 'Meetup dengan teman'],
                ['goal' => 'Self-care routine', 'target' => 'Rutin setiap hari'],
            ],
            $avgMood <= 8 => [
                ['goal' => 'Pertahankan momentum', 'target' => 'Lanjutkan aktivitas positif'],
                ['goal' => 'Bantu orang lain', 'target' => 'Volunteer atau support teman'],
                ['goal' => 'Set goals baru', 'target' => 'Challenge diri sendiri'],
            ],
            default => [
                ['goal' => 'Pertahankan keseimbangan', 'target' => 'Jaga rutinitas sehat'],
                ['goal' => 'Inspirasi orang lain', 'target' => 'Share positivity'],
                ['goal' => 'Grow lebih lanjut', 'target' => 'Set ambitious goals'],
            ],
        };
    }

    /**
     * Get best mood activity
     */
    private function getBestMoodActivity($stats): ?array
    {
        if ($stats->isEmpty()) {
            return null;
        }

        $activityMoods = $stats->groupBy('activity')
            ->map(function ($group) {
                return [
                    'activity' => $group->first()->activity,
                    'avg_mood' => $group->avg('mood'),
                    'count' => $group->count(),
                ];
            })
            ->filter(fn($item) => $item['count'] >= 2) // Only activities done at least 2 times
            ->sortByDesc('avg_mood');

        if ($activityMoods->isEmpty()) {
            return null;
        }

        $bestActivity = $activityMoods->first();
        return [
            'activity' => $bestActivity['activity'],
            'avg_mood' => round($bestActivity['avg_mood'], 1),
            'count' => $bestActivity['count'],
        ];
    }
} 