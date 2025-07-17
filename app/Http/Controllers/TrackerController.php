<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrackerController extends Controller
{
    public function index() {
        return view("tracker.index");
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
        $prompt = "Berikan summary, analisis seperti analisis kecenderungan dan lainnya, dan feedback (jangan terlalu panjang. Pastikan tidak lebih dari 512 token output. gunakan bahasa santai, menenangkan, dan penuh empati, jangan terlalu formal. Jangan gunakan kata sayang seperti pacar. Anda bersifat supportif) untuk seseorang yang mengisi tracker harian dengan data berikut:\n"
            . "Mood: {$validated['mood']}/10\n"
            . "Aktivitas utama: {$validated['activity']}\n"
            . "Penjelasan aktivitas: " . ($validated['activityExplanation'] ?? '-') . "\n"
            . "Energi: {$validated['energy']}/10\n"
            . "Produktivitas: {$validated['productivity']}/10\n"
            . "Hari: " . now()->format('l') . "\n"
            . "Jangan sebutkan bahwa kamu AI. Jawab dengan dekat yang supportif, menenagkan, dan positif.";

        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
        $maxTokens = 512;
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
        return view('tracker.result', ['stat'=> $stat]);
    }
}
