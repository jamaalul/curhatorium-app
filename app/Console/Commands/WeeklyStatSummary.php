<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stat;
use App\Models\WeeklyStat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class WeeklyStatSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weekly:stat-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        // Only run on Sunday
        if ($today->dayOfWeek !== Carbon::SUNDAY) {
            $this->info('Not Sunday. Skipping weekly summary.');
            return 0;
        }
        $weekEnd = $today;
        $weekStart = $today->copy()->subDays(6);

        // Only get users with active Inner Peace membership
        $users = User::whereHas('userMemberships', function($query) {
            $query->where('expires_at', '>', now())
                  ->whereHas('membership', function($q) {
                      $q->where('name', 'Inner Peace');
                  });
        })->get();

        $this->info("Found " . $users->count() . " users with active Inner Peace membership.");

        foreach ($users as $user) {
            $stats = Stat::where('user_id', $user->id)
                ->whereDate('created_at', '>=', $weekStart->toDateString())
                ->whereDate('created_at', '<=', $weekEnd->toDateString())
                ->get();
            
            if ($stats->count() === 0) {
                $this->line("  - User {$user->name}: No stats found for this week");
                continue;
            }
            
            $avgMood = $stats->avg('mood');
            $avgProductivity = $stats->avg('productivity');
            $totalEntries = $stats->count();
            
            // Calculate best mood (highest mood score)
            $bestMood = $stats->max('mood');
            
            // Debug information
            $this->line("  - User {$user->name}: {$totalEntries} entries, avg_mood: {$avgMood}, best_mood: {$bestMood}");
            
            // Validate data before creating
            if ($avgMood <= 0 || $bestMood <= 0) {
                $this->warn("  - User {$user->name}: Invalid mood data (avg: {$avgMood}, best: {$bestMood}), skipping...");
                continue;
            }

            // Compose summary for Gemini
            $moods = $stats->pluck('mood')->implode(', ');
            $activities = $stats->pluck('activity')->implode(', ');
            $explanations = $stats->pluck('explanation')->filter()->implode(' | ');
            $energies = $stats->pluck('energy')->implode(', ');
            $productivities = $stats->pluck('productivity')->implode(', ');

            $prompt = "Berikan summary, analisis kecenderungan, insight, dan feedback (tidak lebih dari 800 token, bahasa santai, menenangkan, empati, tidak formal, tidak sebutkan kamu AI, tidak gunakan kata sayang seperti pacar) untuk rekap tracker mingguan berikut:\n"
                . "Periode: {$weekStart->toDateString()} sampai {$weekEnd->toDateString()}\n"
                . "Mood harian: $moods\n"
                . "Aktivitas utama: $activities\n"
                . "Penjelasan aktivitas: $explanations\n"
                . "Energi harian: $energies\n"
                . "Produktivitas harian: $productivities\n"
                . "Jumlah entri: $totalEntries\n"
                . "Buat analisis dan insight yang supportif, menenangkan, dan positif.";

            $apiKey = env('GEMINI_API_KEY');
            $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
            $maxTokens = 800;
            $temperature = 0.7;
            $feedback = null;
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
                    $feedback = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                }
            } catch (\Exception $e) {
                $feedback = null;
            }

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
            
            $this->line("  - User {$user->name}: Created weekly stat successfully");
        }
        $this->info('WeeklyStat summary created for Inner Peace members only.');
        return 0;
    }
}
