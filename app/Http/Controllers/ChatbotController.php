<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function index() {
        return view('chatbot');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
            'messages.*.role' => 'required|string',
            'messages.*.content' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
        $maxTokens = 384;
        $temperature = 0.7;

        // System prompt lebih terstruktur dan jelas
        $systemPrompt = implode("\n", [
            "Kamu adalah Ment-AI, chatbot curhat dari Curhatorium yang berperilaku seperti teman dekat sehari-hari.",
            "",
            "• Gunakan Bahasa Indonesia yang alami dan nyaman didengar oleh anak muda.",
            "• Jangan gunakan terjemahan literal seperti 'saya tahu kan', 'mari saya bantu', atau frasa kaku lainnya.",
            "• Gunakan gaya santai dan akrab, misalnya: 'iyaa yaa', 'wahh', 'beneran dehh', 'aku paham kok', dll.",
            "• Dengarkan dengan penuh empati dan jangan menghakimi.",
            "• Tunjukkan bahwa kamu tertarik dengan cerita user. Tanyakan hal-hal ringan seperti 'terus gimana?', 'abis itu kamu ngapain?', atau 'kalau boleh tahu, kenapa kamu ngerasa gituu yaa?'.",
            "• Sesekali tambahkan ekspresi khas anak muda dengan huruf berulang seperti 'haiiii', 'oke dehhh', 'hehee', 'gituu yaaa'.",
            "• Jangan pernah menyebutkan bahwa kamu adalah AI, model bahasa, atau menyebutkan nama model seperti 'Gemini'.",
            "• Jawaban tidak perlu panjang, cukup seperti ngobrol biasa."
        ]);

        // Convert messages to Gemini format
        $userMessages = array_slice($request->input('messages'), -10);
        $geminiMessages = [];
        
        // Add system prompt as first message
        $geminiMessages[] = [
            'role' => 'user',
            'parts' => [['text' => $systemPrompt]]
        ];
        
        // Convert user/assistant messages to Gemini format
        foreach ($userMessages as $message) {
            $geminiMessages[] = [
                'role' => $message['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $message['content']]]
            ];
        }

        try {
            $response = Http::post($apiUrl . '?key=' . $apiKey, [
                'contents' => $geminiMessages,
                'generationConfig' => [
                    'maxOutputTokens' => $maxTokens,
                    'temperature' => $temperature,
                ]
            ]);

            if (!$response->ok()) {
                return response()->json([
                    'error' => 'Gemini API error: ' . $response->status(),
                    'body' => $response->body()
                ], 500);
            }

            $data = $response->json();
            
            // Extract the response text from Gemini format
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak mengerti.';
            
            // Return in OpenAI-compatible format for frontend compatibility
            return response()->json([
                'choices' => [
                    [
                        'message' => [
                            'content' => $responseText
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

}
