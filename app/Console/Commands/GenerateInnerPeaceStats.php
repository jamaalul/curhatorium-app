<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stat;
use App\Models\WeeklyStat;
use App\Models\MonthlyStat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GenerateInnerPeaceStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inner-peace:generate-stats {--weeks=4 : Number of weeks to generate} {--months=3 : Number of months to generate} {--regenerate : Regenerate existing stats}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate weekly and monthly stats for Inner Peace members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $weeksToGenerate = $this->option('weeks');
        $monthsToGenerate = $this->option('months');
        $regenerate = $this->option('regenerate');

        // Get users with active Inner Peace membership
        $users = User::whereHas('userMemberships', function($query) {
            $query->where('expires_at', '>', now())
                  ->whereHas('membership', function($q) {
                      $q->where('name', 'Inner Peace');
                  });
        })->get();

        $this->info("Found " . $users->count() . " users with active Inner Peace membership.");

        if ($users->isEmpty()) {
            $this->warn('No users with active Inner Peace membership found.');
            return 0;
        }

        // Clean up invalid stats if regenerating
        if ($regenerate) {
            $this->info("Cleaning up invalid stats...");
            WeeklyStat::where('best_mood', '<=', 0)->delete();
            MonthlyStat::where('best_mood', '<=', 0)->delete();
            $this->info("Invalid stats cleaned up.");
        }

        // Generate weekly stats
        $this->info("Generating weekly stats for the last {$weeksToGenerate} weeks...");
        $this->generateWeeklyStats($users, $weeksToGenerate, $regenerate);

        // Generate monthly stats
        $this->info("Generating monthly stats for the last {$monthsToGenerate} months...");
        $this->generateMonthlyStats($users, $monthsToGenerate, $regenerate);

        $this->info('Stats generation completed successfully!');
        return 0;
    }

    private function generateWeeklyStats($users, $weeksToGenerate, $regenerate)
    {
        $today = Carbon::today();
        
        for ($i = 0; $i < $weeksToGenerate; $i++) {
            $weekEnd = $today->copy()->subWeeks($i)->endOfWeek();
            $weekStart = $weekEnd->copy()->subDays(6);

            $this->info("Processing week: {$weekStart->toDateString()} to {$weekEnd->toDateString()}");

            foreach ($users as $user) {
                // Check if weekly stat already exists for this week
                $existingStat = WeeklyStat::where('user_id', $user->id)
                    ->where('week_start', $weekStart->toDateString())
                    ->where('week_end', $weekEnd->toDateString())
                    ->first();

                if ($existingStat && !$regenerate) {
                    $this->line("  - User {$user->name}: Weekly stat already exists, skipping...");
                    continue;
                }

                $stats = Stat::where('user_id', $user->id)
                    ->whereDate('created_at', '>=', $weekStart->toDateString())
                    ->whereDate('created_at', '<=', $weekEnd->toDateString())
                    ->get();

                if ($stats->count() === 0) {
                    $this->line("  - User {$user->name}: No daily stats found for this week");
                    continue;
                }

                $avgMood = $stats->avg('mood');
                $avgProductivity = $stats->avg('productivity');
                $totalEntries = $stats->count();
                $bestMood = $stats->max('mood');

                // Validate data before creating
                if ($avgMood <= 0 || $bestMood <= 0) {
                    $this->line("  - User {$user->name}: Invalid mood data (avg: {$avgMood}, best: {$bestMood}), skipping...");
                    continue;
                }

                // Generate AI feedback
                $feedback = $this->generateAIFeedback($stats, $weekStart, $weekEnd, 'weekly');

                WeeklyStat::create([
                    'user_id' => $user->id,
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),
                    'avg_mood' => round($avgMood, 2),
                    'avg_productivity' => round($avgProductivity, 2),
                    'total_entries' => $totalEntries,
                    'best_mood' => $bestMood,
                    'feedback' => $feedback,
                ]);

                $this->line("  - User {$user->name}: Created weekly stat with {$totalEntries} entries (avg_mood: {$avgMood}, best_mood: {$bestMood})");
            }
        }
    }

    private function generateMonthlyStats($users, $monthsToGenerate, $regenerate)
    {
        $today = Carbon::today();
        
        for ($i = 1; $i <= $monthsToGenerate; $i++) {
            $monthEnd = $today->copy()->subMonths($i)->endOfMonth();
            $monthStart = $monthEnd->copy()->startOfMonth();
            $monthLabel = $monthStart->format('F Y');

            $this->info("Processing month: {$monthStart->toDateString()} to {$monthEnd->toDateString()}");

            foreach ($users as $user) {
                // Check if monthly stat already exists for this month
                $existingStat = MonthlyStat::where('user_id', $user->id)
                    ->where('month', $monthLabel)
                    ->first();

                if ($existingStat && !$regenerate) {
                    $this->line("  - User {$user->name}: Monthly stat already exists, skipping...");
                    continue;
                }

                $stats = Stat::where('user_id', $user->id)
                    ->whereDate('created_at', '>=', $monthStart->toDateString())
                    ->whereDate('created_at', '<=', $monthEnd->toDateString())
                    ->get();

                if ($stats->count() === 0) {
                    $this->line("  - User {$user->name}: No daily stats found for this month");
                    continue;
                }

                $avgMood = $stats->avg('mood');
                $avgProductivity = $stats->avg('productivity');
                $totalEntries = $stats->count();
                $bestMood = $stats->max('mood');

                // Calculate weekly averages from daily stats
                $weeklyAverages = [];
                $currentWeek = null;
                $weekStats = [];
                
                foreach($stats as $stat) {
                    $weekStart = Carbon::parse($stat->created_at)->startOfWeek();
                    $weekKey = $weekStart->format('Y-W');
                    
                    if ($currentWeek !== $weekKey) {
                        if ($currentWeek && !empty($weekStats)) {
                            $weeklyAverages[] = array_sum($weekStats) / count($weekStats);
                        }
                        $currentWeek = $weekKey;
                        $weekStats = [];
                    }
                    $weekStats[] = $stat->mood;
                }
                
                if (!empty($weekStats)) {
                    $weeklyAverages[] = array_sum($weekStats) / count($weekStats);
                }
                
                $avgWeeklyMood = !empty($weeklyAverages) ? array_sum($weeklyAverages) / count($weeklyAverages) : $avgMood;
                
                // Calculate mood fluctuation (standard deviation)
                $moodValues = $stats->pluck('mood')->toArray();
                $moodFluctuation = !empty($moodValues) ? sqrt(array_sum(array_map(function($x) use ($avgWeeklyMood) { 
                    return pow($x - $avgWeeklyMood, 2); 
                }, $moodValues)) / count($moodValues)) : 0;
                
                // Calculate good vs low mood days
                $goodMoodDays = $stats->where('mood', '>=', 7)->count();
                $lowMoodDays = $stats->where('mood', '<=', 4)->count();
                
                // Most frequent mood
                $moodCounts = $stats->groupBy('mood')->map->count();
                $mostFrequentMood = $moodCounts->sortDesc()->keys()->first();
                
                // Calculate activity analysis
                $activityStats = $stats->groupBy('activity')->map(function($group) {
                    return [
                        'count' => $group->count(),
                        'avg_mood' => $group->avg('mood'),
                        'avg_productivity' => $group->avg('productivity'),
                    ];
                });
                
                $mostFrequentActivity = $activityStats->sortByDesc('count')->keys()->first();
                $bestMoodActivity = $activityStats->sortByDesc('avg_mood')->keys()->first();
                $worstMoodActivity = $activityStats->sortBy('avg_mood')->keys()->first();
                
                $activityAnalysis = [
                    'most_frequent' => [
                        'activity' => $mostFrequentActivity,
                        'stats' => $activityStats[$mostFrequentActivity] ?? null,
                    ],
                    'best_mood' => [
                        'activity' => $bestMoodActivity,
                        'stats' => $activityStats[$bestMoodActivity] ?? null,
                    ],
                    'worst_mood' => [
                        'activity' => $worstMoodActivity,
                        'stats' => $activityStats[$worstMoodActivity] ?? null,
                    ],
                ];
                
                // Calculate productivity analysis
                $highestProductivityDay = $stats->sortByDesc('productivity')->first();
                $lowestProductivityDay = $stats->sortBy('productivity')->first();
                
                // Calculate weekly productivity averages
                $weeklyProductivity = [];
                $currentWeek = null;
                $weekProductivity = [];
                
                foreach($stats as $stat) {
                    $weekStart = Carbon::parse($stat->created_at)->startOfWeek();
                    $weekKey = $weekStart->format('Y-W');
                    
                    if ($currentWeek !== $weekKey) {
                        if ($currentWeek && !empty($weekProductivity)) {
                            $weeklyProductivity[] = array_sum($weekProductivity) / count($weekProductivity);
                        }
                        $currentWeek = $weekKey;
                        $weekProductivity = [];
                    }
                    $weekProductivity[] = $stat->productivity;
                }
                
                if (!empty($weekProductivity)) {
                    $weeklyProductivity[] = array_sum($weekProductivity) / count($weekProductivity);
                }
                
                $highestProductivityWeek = !empty($weeklyProductivity) ? max($weeklyProductivity) : 0;
                $lowestProductivityWeek = !empty($weeklyProductivity) ? min($weeklyProductivity) : 0;
                
                $productivityAnalysis = [
                    'highest_day' => $highestProductivityDay ? [
                        'date' => $highestProductivityDay->created_at->toDateString(),
                        'productivity' => $highestProductivityDay->productivity,
                        'mood' => $highestProductivityDay->mood,
                        'activity' => $highestProductivityDay->activity,
                    ] : null,
                    'lowest_day' => $lowestProductivityDay ? [
                        'date' => $lowestProductivityDay->created_at->toDateString(),
                        'productivity' => $lowestProductivityDay->productivity,
                        'mood' => $lowestProductivityDay->mood,
                        'activity' => $lowestProductivityDay->activity,
                    ] : null,
                    'highest_week' => $highestProductivityWeek,
                    'lowest_week' => $lowestProductivityWeek,
                ];
                
                // Calculate pattern analysis
                $lowMoodPercentage = $totalEntries > 0 ? ($lowMoodDays / $totalEntries) * 100 : 0;
                $isHighFluctuation = $moodFluctuation > 2.0;
                $isMostlyPositive = $avgWeeklyMood > 7.0;
                
                $patternAnalysis = [
                    'low_mood_percentage' => $lowMoodPercentage,
                    'is_high_fluctuation' => $isHighFluctuation,
                    'is_mostly_positive' => $isMostlyPositive,
                    'pattern_type' => $lowMoodPercentage > 50 ? 'chronic_stress' : 
                                    ($isHighFluctuation ? 'high_fluctuation' : 
                                    ($isMostlyPositive ? 'mostly_positive' : 'balanced')),
                ];

                // Validate data before creating
                if ($avgMood <= 0 || $bestMood <= 0) {
                    $this->line("  - User {$user->name}: Invalid mood data (avg: {$avgMood}, best: {$bestMood}), skipping...");
                    continue;
                }

                // Generate AI feedback
                $feedback = $this->generateAIFeedback($stats, $monthStart, $monthEnd, 'monthly');
                
                // Debug information
                $this->line("  - User {$user->name}: Created monthly stat with {$totalEntries} entries (avg_mood: {$avgMood}, best_mood: {$bestMood})");

                MonthlyStat::create([
                    'user_id' => $user->id,
                    'month' => $monthLabel,
                    'avg_mood' => round($avgMood, 2),
                    'avg_weekly_mood' => round($avgWeeklyMood, 2),
                    'mood_fluctuation' => round($moodFluctuation, 2),
                    'good_mood_days' => $goodMoodDays,
                    'low_mood_days' => $lowMoodDays,
                    'most_frequent_mood' => $mostFrequentMood,
                    'avg_productivity' => round($avgProductivity, 2),
                    'total_entries' => $totalEntries,
                    'best_mood' => $bestMood,
                    'activity_analysis' => $activityAnalysis,
                    'productivity_analysis' => $productivityAnalysis,
                    'pattern_analysis' => $patternAnalysis,
                    'feedback' => $feedback,
                ]);

                $this->line("  - User {$user->name}: Created monthly stat with {$totalEntries} entries (avg_mood: {$avgMood}, best_mood: {$bestMood})");
            }
        }
    }

    private function generateAIFeedback($stats, $startDate, $endDate, $type)
    {
        $moods = $stats->pluck('mood')->implode(', ');
        $activities = $stats->pluck('activity')->implode(', ');
        $explanations = $stats->pluck('explanation')->filter()->implode(' | ');
        $energies = $stats->pluck('energy')->implode(', ');
        $productivities = $stats->pluck('productivity')->implode(', ');
        $totalEntries = $stats->count();

        $periodText = $type === 'weekly' ? 'mingguan' : 'bulanan';
        
        $prompt = "Berikan summary, analisis kecenderungan, insight, dan feedback (tidak lebih dari 800 token, bahasa santai, menenangkan, empati, tidak formal, tidak sebutkan kamu AI, tidak gunakan kata sayang seperti pacar) untuk rekap tracker {$periodText} berikut:\n"
            . "Periode: {$startDate->toDateString()} sampai {$endDate->toDateString()}\n"
            . "Mood harian: $moods\n"
            . "Aktivitas utama: $activities\n"
            . "Penjelasan aktivitas: $explanations\n"
            . "Energi harian: $energies\n"
            . "Produktivitas harian: $productivities\n"
            . "Jumlah entri: $totalEntries\n"
            . "Buat analisis dan insight yang supportif, menenangkan, dan positif.";

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return null;
        }

        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
        $maxTokens = 800;
        $temperature = 0.7;

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
                    'maxOutputTokens' => $maxTokens,
                    'temperature' => $temperature,
                ]
            ]);

            if ($response->ok()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }
        } catch (\Exception $e) {
            // Log error but don't fail the command
            $this->warn("Failed to generate AI feedback: " . $e->getMessage());
        }

        return null;
    }
} 