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

                // Validate data before creating
                if ($avgMood <= 0 || $bestMood <= 0) {
                    $this->line("  - User {$user->name}: Invalid mood data (avg: {$avgMood}, best: {$bestMood}), skipping...");
                    continue;
                }

                // Generate AI feedback
                $feedback = $this->generateAIFeedback($stats, $monthStart, $monthEnd, 'monthly');

                MonthlyStat::create([
                    'user_id' => $user->id,
                    'month' => $monthLabel,
                    'avg_mood' => round($avgMood, 2),
                    'avg_productivity' => round($avgProductivity, 2),
                    'total_entries' => $totalEntries,
                    'best_mood' => $bestMood,
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