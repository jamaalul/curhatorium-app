<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stat;
use App\Models\MonthlyStat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class MonthlyStatSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:stat-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly stat summary for each user with AI feedback.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        // Only run on the first day of the month
        if ($today->day !== 1) {
            $this->info('Not the first day of the month. Skipping monthly summary.');
            return 0;
        }
        $monthStart = $today->copy()->subMonth()->startOfMonth();
        $monthEnd = $today->copy()->subMonth()->endOfMonth();
        $monthLabel = $monthStart->format('F Y');

        $users = User::all();
        foreach ($users as $user) {
            $stats = Stat::where('user_id', $user->id)
                ->whereDate('created_at', '>=', $monthStart->toDateString())
                ->whereDate('created_at', '<=', $monthEnd->toDateString())
                ->get();
            if ($stats->count() === 0) {
                continue;
            }
            $avgMood = $stats->avg('mood');
            $avgProductivity = $stats->avg('productivity');
            $totalEntries = $stats->count();
            
            // Calculate best mood (highest mood score)
            $bestMood = $stats->max('mood');

            // Compose summary for Gemini
            $moods = $stats->pluck('mood')->implode(', ');
            $activities = $stats->pluck('activity')->implode(', ');
            $explanations = $stats->pluck('explanation')->filter()->implode(' | ');
            $energies = $stats->pluck('energy')->implode(', ');
            $productivities = $stats->pluck('productivity')->implode(', ');

            $prompt = "Berikan summary, analisis kecenderungan, insight, dan feedback (tidak lebih dari 800 token, bahasa santai, menenangkan, empati, tidak formal, tidak sebutkan kamu AI, tidak gunakan kata sayang seperti pacar) untuk rekap tracker bulanan berikut:\n"
                . "Periode: {$monthStart->toDateString()} sampai {$monthEnd->toDateString()}\n"
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

            MonthlyStat::create([
                'user_id' => $user->id,
                'month' => $monthLabel,
                'avg_mood' => $avgMood,
                'avg_productivity' => $avgProductivity,
                'total_entries' => $totalEntries,
                'best_mood' => $bestMood,
                'feedback' => $feedback,
            ]);
        }
        $this->info('MonthlyStat summary created for all users.');
        return 0;
    }
}
