<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ChatbotSession;
use App\Models\ChatbotMessage;

class ChatbotController extends Controller
{
    public function index() {
        $sessions = ChatbotSession::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('chatbot', compact('sessions'));
    }

    public function getSessions() {
        $sessions = ChatbotSession::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return response()->json($sessions);
    }

    public function getSession($sessionId) {
        $session = ChatbotSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->with('messages')
            ->firstOrFail();
        
        return response()->json($session);
    }

    public function createSession(Request $request) {
        $session = ChatbotSession::create([
            'user_id' => Auth::id(),
            'title' => $request->input('title', 'New Chat')
        ]);

        // Add initial bot message
        ChatbotMessage::create([
            'chatbot_session_id' => $session->id,
            'role' => 'assistant',
            'content' => 'Haiiii. Ada cerita apa hari ini?'
        ]);

        return response()->json($session->load('messages'));
    }

    public function deleteSession($sessionId) {
        $session = ChatbotSession::where('id', $sessionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $session->delete();
        
        return response()->json(['success' => true]);
    }

    public function chat(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chatbot_sessions,id',
            'message' => 'required|string',
        ]);

        $session = ChatbotSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Save user message
        ChatbotMessage::create([
            'chatbot_session_id' => $session->id,
            'role' => 'user',
            'content' => $request->message
        ]);

        // Get recent messages for context (last 10 messages)
        $recentMessages = $session->messages()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

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
        $geminiMessages = [];
        
        // Add system prompt as first message
        $geminiMessages[] = [
            'role' => 'user',
            'parts' => [['text' => $systemPrompt]]
        ];
        
        // Convert user/assistant messages to Gemini format
        foreach ($recentMessages as $message) {
            $geminiMessages[] = [
                'role' => $message->role === 'user' ? 'user' : 'model',
                'parts' => [['text' => $message->content]]
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
            
            // Save bot response
            ChatbotMessage::create([
                'chatbot_session_id' => $session->id,
                'role' => 'assistant',
                'content' => $responseText
            ]);

            // Update session title if it's still the default
            if (!$session->title || $session->title === 'New Chat') {
                $session->update(['title' => Str::limit($request->message, 50)]);
            }
            
            return response()->json([
                'message' => $responseText,
                'session' => $session->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
