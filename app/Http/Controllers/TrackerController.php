<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\WeeklyStat;
use App\Models\MonthlyStat;

class TrackerController extends Controller
{
    public function index() {
        $existing = Stat::where('user_id', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if ($existing) {
        // Optionally, you can redirect back with an error message
            return redirect()->back()->withErrors(['msg' => 'You have already submitted your tracker for today.']);
        } else {
            return view("tracker.index");
        }
    }

    public function track(Request $request) {
        // Log the incoming request data for debugging
        Log::info('Tracker form submission', $request->all());
        
        // Validate the request
        $validated = $request->validate([
            'mood' => 'required|integer|min:1|max:10',
            'activity' => 'required|string|in:work,exercise,social,hobbies,rest,entertainment,nature,food,health,other',
            'activityExplanation' => 'nullable|string|max:500',
            'energy' => 'required|integer|min:1|max:10',
            'productivity' => 'required|integer|min:1|max:10',
        ]);

        // Compose prompt for Gemini API
        $prompt = "Berikan summary, analisis seperti analisis kecenderungan dan lainnya, dan feedback (jangan terlalu panjang. Pastikan tidak lebih dari 800 token output. gunakan bahasa santai, menenangkan, dan penuh empati, jangan terlalu formal. Jangan gunakan kata sayang seperti pacar. Anda bersifat supportif) untuk seseorang yang mengisi tracker harian dengan data berikut:\n"
                . "Mood: {$validated['mood']}/10 (" . match (true) {
                    $validated['mood'] <= 2 => "Sangat Negatif - disarankan untuk istirahat dan cari aktivitas yang menenangkan.",
                    $validated['mood'] <= 4 => "Negatif - coba luangkan waktu buat refleksi atau ngobrol dengan orang yang dipercaya.",
                    $validated['mood'] <= 6 => "Netral - kamu cukup stabil, tapi bisa coba sesuatu yang nyenengin diri.",
                    $validated['mood'] <= 8 => "Positif - suasana hatimu bagus, pertahankan dan terus eksplor hal-hal positif.",
                    default => "Sangat Positif - kamu lagi di titik yang baik banget, semoga bisa terus stabil ya.",
                } . ")\n"
                . "Aktivitas utama: {$validated['activity']}\n"
                . "Penjelasan aktivitas: " . ($validated['activityExplanation'] ?? '-') . "\n"
                . "Energi: {$validated['energy']}/10\n"
                . "Produktivitas: {$validated['productivity']}/10\n"
                . "Hari: " . now()->format('l') . "\n"
                . "Jangan sebutkan bahwa kamu AI. Jawab dengan dekat yang supportif, menenangkan, dan positif.";


        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
        $maxTokens = 800;
        $temperature = 0.7;

        try {
            $response = \Illuminate\Support\Facades\Http::post($apiUrl . '?key=' . $apiKey, [
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
                Log::info('Full Gemini API response data:', $data);
                
                $feedback = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                Log::info('Extracted feedback text:', ['feedback' => $feedback, 'length' => strlen($feedback ?? '')]);
            } else {
                $feedback = null;
                Log::error('Gemini API error for tracker feedback', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            $feedback = null;
            Log::error('Gemini API exception for tracker feedback', [
                'error' => $e->getMessage()
            ]);
        }

        // Create the stat record
        $stat = Stat::create([
            'user_id' => Auth::user()->id,
            'mood' => $validated['mood'],
            'activity' => $validated['activity'],
            'explanation' => $validated['activityExplanation'],
            'energy' => $validated['energy'],
            'productivity' => $validated['productivity'],
            'day' => now()->format('l'), // e.g., 'Monday', 'Tuesday', etc.
            'feedback' => $feedback,
        ]);

        Log::info('Mood entry created successfully', ['stat_id' => $stat->id]);

        // Return success response
        return redirect()->route('tracker.result');
    }

    public function result() {
        $stat = Stat::where('user_id', Auth::user()->id)
            ->whereDate('created_at', now()->toDateString())
            ->first();

        return view('tracker.result', ['stat' => $stat]);
    }

    public function history() {
        return view('tracker.history.index');
    }

    public function showStat($id) {
        $stat = Stat::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        return view('tracker.stat-detail', compact('stat'));
    }

    public function showWeeklyStat($id) {
        // Check if user has active Inner Peace membership
        if (!Auth::user()->hasActiveInnerPeaceMembership()) {
            return redirect()->route('membership.index')
                ->withErrors(['msg' => 'Fitur ini hanya tersedia untuk member Inner Peace. Silakan upgrade membership Anda untuk mengakses laporan mingguan dan bulanan.']);
        }

        $weeklyStat = WeeklyStat::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        // Get the individual stats for this week
        $stats = Stat::where('user_id', Auth::id())
            ->whereBetween('created_at', [$weeklyStat->week_start, $weeklyStat->week_end])
            ->orderBy('created_at', 'asc')
            ->get();

        // Enhanced analysis data
        $analysis = $this->generateWeeklyAnalysis($stats, $weeklyStat);
        
        return view('tracker.weekly-stat-detail', compact('weeklyStat', 'stats', 'analysis'));
    }

    private function generateWeeklyAnalysis($stats, $weeklyStat)
    {
        if ($stats->isEmpty()) {
            return null;
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

    private function getMoodDescription($avgMood)
    {
        if ($avgMood <= 2) return 'Sangat Negatif';
        if ($avgMood <= 4) return 'Negatif';
        if ($avgMood <= 6) return 'Netral';
        if ($avgMood <= 8) return 'Positif';
        return 'Sangat Positif';
    }

    private function getMoodRecommendation($avgMood)
    {
        if ($avgMood <= 4) return 'Meningkatkan Mood';
        if ($avgMood <= 6) return 'Menjaga Mood';
        return 'Mempertahankan Mood';
    }

    private function getMoodRangeDescription($range)
    {
        if ($range <= 2) return 'Stabil';
        if ($range <= 4) return 'Sedang';
        return 'Fluktuatif';
    }

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

    private function getGoalTable($avgMood)
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

    public function showMonthlyStat($id) {
        // Check if user has active Inner Peace membership
        if (!Auth::user()->hasActiveInnerPeaceMembership()) {
            return redirect()->route('membership.index')
                ->withErrors(['msg' => 'Fitur ini hanya tersedia untuk member Inner Peace. Silakan upgrade membership Anda untuk mengakses laporan mingguan dan bulanan.']);
        }

        $monthlyStat = MonthlyStat::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        // Convert month string to Carbon date for querying
        $monthDate = \Carbon\Carbon::parse($monthlyStat->month);
        
        // Get the individual stats for this month
        $stats = Stat::where('user_id', Auth::id())
            ->whereYear('created_at', $monthDate->year)
            ->whereMonth('created_at', $monthDate->month)
            ->orderBy('created_at', 'asc')
            ->get();
        
        return view('tracker.monthly-stat-detail', compact('monthlyStat', 'stats'));
    }

    // API: Get paginated stats
    public function getStats(Request $request) {
        $stats = Stat::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);
        return response()->json($stats);
    }

    // API: Get paginated weekly stats
    public function getWeeklyStats(Request $request) {
        // Check if user has active Inner Peace membership
        if (!Auth::user()->hasActiveInnerPeaceMembership()) {
            return response()->json(['error' => 'Fitur ini hanya tersedia untuk member Inner Peace'], 403);
        }

        $weeklyStats = WeeklyStat::where('user_id', Auth::id())
            ->orderByDesc('week_start')
            ->paginate(10);
        return response()->json($weeklyStats);
    }

    // API: Get paginated monthly stats
    public function getMonthlyStats(Request $request) {
        // Check if user has active Inner Peace membership
        if (!Auth::user()->hasActiveInnerPeaceMembership()) {
            return response()->json(['error' => 'Fitur ini hanya tersedia untuk member Inner Peace'], 403);
        }

        $monthlyStats = MonthlyStat::where('user_id', Auth::id())
            ->orderByDesc('month')
            ->paginate(10);
        return response()->json($monthlyStats);
    }

    // Get stats data for dashboard
    public function getStatsForDashboard() {
        $userId = Auth::id();
        
        // Get stats from the last 7 days
        $sevenDaysAgo = now()->subDays(6)->startOfDay();
        $stats = Stat::where('user_id', $userId)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Create a map of day names (Sunday = 0, Monday = 1, etc.)
        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $chartData = [];
        
        // Initialize chart data for the last 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays(6 - $i);
            $dayName = $dayNames[$date->dayOfWeek]; // dayOfWeek is 0-6, matching array index
            
            // Find if we have data for this date
            $stat = $stats->where('created_at', '>=', $date->startOfDay())
                         ->where('created_at', '<=', $date->endOfDay())
                         ->first();
            
            if ($stat) {
                $chartData[] = [
                    'day' => $dayName,
                    'value' => $stat->mood,
                    'productivity' => $stat->productivity
                ];
            }
            // Skip days without data - don't add them to chartData
        }
        
        // Calculate averages (only from days with data)
        $moodValues = collect($chartData)->pluck('value');
        $productivityValues = collect($chartData)->pluck('productivity');
        
        $averageMood = $moodValues->count() > 0 ? number_format($moodValues->avg(), 2) : '0.00';
        $averageProd = $productivityValues->count() > 0 ? number_format($productivityValues->avg(), 2) : '0.00';
        
        return [
            'chartData' => $chartData,
            'averageMood' => $averageMood,
            'averageProd' => $averageProd
        ];
    }
}
