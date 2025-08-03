<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TrackerService;
use App\Http\Requests\TrackerRequest;
use App\Models\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrackerController extends Controller
{
    public function __construct(
        private TrackerService $trackerService
    ) {}
    public function index() {
        $user = Auth::user();
        
        // Debug: Check user's tickets and memberships
        $tickets = $user->userTickets()->where('ticket_type', 'tracker')->get();
        $memberships = $user->activeMemberships()->get();
        
        Log::info('User tracker access check:', [
            'user_id' => $user->id,
            'tracker_tickets' => $tickets->toArray(),
            'active_memberships' => $memberships->toArray(),
        ]);
        
        if ($this->trackerService->hasTrackedToday($user)) {
            return redirect()->back()->withErrors(['msg' => 'You have already submitted your tracker for today.']);
        }
        
        return view("tracker.index");
    }

    public function track(TrackerRequest $request) {
        try {
            Log::info('Tracker form submission', $request->all());
            Log::info('Validated data:', $request->validated());
            
            $user = Auth::user();
            Log::info('User ID:', ['user_id' => $user->id]);
            
            $result = $this->trackerService->createTrackerEntry($user, $request->validated());
            
            Log::info('Mood entry created successfully', ['stat_id' => $result['stat']['id']]);
            
            return redirect()->route('tracker.result');
        } catch (\Exception $e) {
            Log::error('Tracker submission error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withErrors(['msg' => 'Failed to submit tracker. Please try again.']);
        }
    }

    public function result() {
        $user = Auth::user();
        // Get the most recent stat as a model object instead of array
        $stat = Stat::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('tracker.result', ['stat' => $stat]);
    }

    public function history() {
        return view('tracker.history.index');
    }

    public function showStat($id) {
        $user = Auth::user();
        $stat = $this->trackerService->getStat($id, $user);
        
        if (!$stat) {
            abort(404);
        }
        
        return view('tracker.stat-detail', compact('stat'));
    }

    public function showWeeklyStat($id) {
        $user = Auth::user();
        
        // Check if user has active Inner Peace membership
        if (!$user->hasActiveInnerPeaceMembership()) {
            return redirect()->route('membership.index')
                ->withErrors(['msg' => 'Fitur ini hanya tersedia untuk member Inner Peace. Silakan upgrade membership Anda untuk mengakses laporan mingguan dan bulanan.']);
        }

        $data = $this->trackerService->getWeeklyStatDetail($id, $user);
        
        if (!$data) {
            abort(404);
        }
        
        return view('tracker.weekly-stat-detail', $data);
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
        $user = Auth::user();
        $stats = $this->trackerService->getUserStats($user, 10);
        
        return response()->json($stats);
    }

    // API: Get paginated weekly stats
    public function getWeeklyStats(Request $request) {
        $user = Auth::user();
        
        // Check if user has active Inner Peace membership
        if (!$user->hasActiveInnerPeaceMembership()) {
            return response()->json(['error' => 'Fitur ini hanya tersedia untuk member Inner Peace'], 403);
        }

        $weeklyStats = $this->trackerService->getWeeklyStats($user);
        
        return response()->json($weeklyStats);
    }

    // API: Get paginated monthly stats
    public function getMonthlyStats(Request $request) {
        $user = Auth::user();
        
        // Check if user has active Inner Peace membership
        if (!$user->hasActiveInnerPeaceMembership()) {
            return response()->json(['error' => 'Fitur ini hanya tersedia untuk member Inner Peace'], 403);
        }

        $monthlyStats = $this->trackerService->getMonthlyStats($user);
        
        return response()->json($monthlyStats);
    }

    // Get stats data for dashboard
    public function getStatsForDashboard() {
        try {
            $user = Auth::user();
            $stats = $this->trackerService->getStatsForDashboard($user);
            
            // Log the result for debugging (only in development)
            if (config('app.debug')) {
                Log::info('Dashboard stats data:', $stats);
            }
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Error in getStatsForDashboard: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return safe fallback data
            return [
                'chartData' => [],
                'averageMood' => '0.00',
                'averageProd' => '0.00'
            ];
        }
    }
}
