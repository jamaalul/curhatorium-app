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
        $weeklyStat = WeeklyStat::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        // Get the individual stats for this week
        $stats = Stat::where('user_id', Auth::id())
            ->whereBetween('created_at', [$weeklyStat->week_start, $weeklyStat->week_end])
            ->orderBy('created_at', 'asc')
            ->get();
        
        return view('tracker.weekly-stat-detail', compact('weeklyStat', 'stats'));
    }

    public function showMonthlyStat($id) {
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
        $weeklyStats = WeeklyStat::where('user_id', Auth::id())
            ->orderByDesc('week_start')
            ->paginate(10);
        return response()->json($weeklyStats);
    }

    // API: Get paginated monthly stats
    public function getMonthlyStats(Request $request) {
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
