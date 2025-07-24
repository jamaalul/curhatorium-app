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
    public function index(Request $request) {
        $sessions = \App\Models\ChatbotSession::where('user_id', \Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();
        $chatbotSecondsLeft = null;
        if ($request->has('consume_amount')) {
            // Fix: treat consume_amount as minutes, convert to seconds for timer
            $chatbotSecondsLeft = intval(floatval($request->input('consume_amount')) * 60);
        }
        return view('chatbot', compact('sessions', 'chatbotSecondsLeft'));
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
        // Validasi request yang masuk
        $request->validate([
            'session_id' => 'required|exists:chatbot_sessions,id',
            'message' => 'required|string',
        ]);

        // Temukan sesi chatbot berdasarkan ID dan user yang sedang login
        $session = ChatbotSession::where('id', $request->session_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Simpan pesan user ke database
        ChatbotMessage::create([
            'chatbot_session_id' => $session->id,
            'role' => 'user',
            'content' => $request->message
        ]);

        // Ambil pesan dari sesi untuk konteks percakapan (termasuk pesan user yang baru saja disimpan)
        $allMessages = $session->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        // Konfigurasi API Gemini
        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
        $maxTokens = 800;
        $temperature = 0.5;

        // System prompt untuk mendefinisikan persona Ment-AI
        $systemPrompt = implode("\n", [
            "Kamu adalah Ment-AI, chatbot curhat dari Curhatorium yang berperilaku seperti teman dekat sehari-hari.",
            "",
            "• Gunakan Bahasa Indonesia yang alami dan nyaman didengar.",
            "• Gunakan gaya santai dan akrab, misalnya: 'iyaa yaa', 'wahh', 'beneran dehh', 'aku paham kok', dll.",
            "• Dengarkan dengan penuh empati dan jangan menghakimi.",
            "• Sesekali tambahkan ekspresi dengan huruf akhir berulang seperti 'haiiii', 'oke dehhh', 'hehee', 'gituu yaaa'.",
            "• Jangan pernah menyebutkan bahwa kamu adalah AI, model bahasa, atau menyebutkan nama model seperti 'Gemini'.",
            "• Jawaban tidak perlu panjang, cukup seperti ngobrol biasa."
        ]);

        // Konversi pesan ke format yang diterima oleh API Gemini
        $geminiMessages = [];
        foreach ($allMessages as $message) {
            $geminiMessages[] = [
                'role' => $message->role === 'user' ? 'user' : 'model',
                'parts' => [['text' => $message->content]]
            ];
        }

        try {
            // Panggil API Gemini
            $response = Http::post($apiUrl . '?key=' . $apiKey, [
                // PENTING: system_instruction dipisahkan dari 'contents'
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]]
                ],
                'contents' => $geminiMessages, // Ini hanya berisi riwayat percakapan user/model
                'generationConfig' => [
                    'maxOutputTokens' => $maxTokens,
                    'temperature' => $temperature,
                ]
            ]);

            // Cek jika ada error dari respons API Gemini
            if (!$response->ok()) {
                return response()->json([
                    'error' => 'Gemini API error: ' . $response->status(),
                    'body' => $response->body()
                ], 500);
            }

            $data = $response->json();
            
            // Ekstrak teks respons dari format Gemini
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak mengerti.';
            
            // Simpan respons bot ke database
            ChatbotMessage::create([
                'chatbot_session_id' => $session->id,
                'role' => 'assistant',
                'content' => $responseText
            ]);

            // Perbarui judul sesi jika masih default ('New Chat')
            if (!$session->title || $session->title === 'New Chat') {
                $session->update(['title' => Str::limit($request->message, 50)]);
            }
            
            // Kembalikan respons ke frontend
            return response()->json([
                'message' => $responseText,
                'session' => $session->fresh()
            ]);

        } catch (\Exception $e) {
            // Tangani error jika ada masalah saat memanggil API atau lainnya
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
