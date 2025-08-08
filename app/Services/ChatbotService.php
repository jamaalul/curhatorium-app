<?php

namespace App\Services;

use App\Models\ChatbotSession;
use App\Models\ChatbotMessage;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Get user's chatbot sessions
     */
    public function getUserSessions(User $user): array
    {
        $sessions = ChatbotSession::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'title' => $session->title,
                'created_at' => $session->created_at,
                'updated_at' => $session->updated_at
            ];
        })->toArray();
    }

    /**
     * Get specific session with messages
     */
    public function getSession(int $sessionId, User $user): ?array
    {
        $session = ChatbotSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->with('messages')
            ->first();

        if (!$session) {
            return null;
        }

        return [
            'id' => $session->id,
            'title' => $session->title,
            'created_at' => $session->created_at,
            'updated_at' => $session->updated_at,
            'messages' => $session->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'role' => $message->role,
                    'content' => $message->content,
                    'created_at' => $message->created_at
                ];
            })->toArray()
        ];
    }

    /**
     * Create a new chatbot session
     */
    public function createSession(User $user, string $title = 'New Chat'): array
    {
        return DB::transaction(function () use ($user, $title) {
            $session = ChatbotSession::create([
                'user_id' => $user->id,
                'title' => $title
            ]);

            // Add initial bot message
            ChatbotMessage::create([
                'chatbot_session_id' => $session->id,
                'role' => 'assistant',
                'content' => 'Haiiii. Ada cerita apa hari ini?'
            ]);

            $sessionWithMessages = $session->load('messages');
            
            return [
                'id' => $sessionWithMessages->id,
                'title' => $sessionWithMessages->title,
                'created_at' => $sessionWithMessages->created_at,
                'updated_at' => $sessionWithMessages->updated_at,
                'messages' => $sessionWithMessages->messages->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'role' => $message->role,
                        'content' => $message->content,
                        'created_at' => $message->created_at
                    ];
                })->toArray()
            ];
        });
    }

    /**
     * Delete a chatbot session
     */
    public function deleteSession(int $sessionId, User $user): bool
    {
        return DB::transaction(function () use ($sessionId, $user) {
            $session = ChatbotSession::where('id', $sessionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$session) {
                return false;
            }

            $session->delete();
            return true;
        });
    }

    /**
     * Process chat message and get AI response
     */
    public function processChatMessage(int $sessionId, string $message, User $user): array
    {
        return DB::transaction(function () use ($sessionId, $message, $user) {
            // Find session
            $session = ChatbotSession::where('id', $sessionId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Save user message
            ChatbotMessage::create([
                'chatbot_session_id' => $session->id,
                'role' => 'user',
                'content' => $message
            ]);

            // Get conversation context
            $allMessages = $session->messages()
                ->orderBy('created_at', 'asc')
                ->get();

            // Get AI response
            $aiResponse = $this->getGeminiResponse($allMessages);

            // Save AI response
            ChatbotMessage::create([
                'chatbot_session_id' => $session->id,
                'role' => 'assistant',
                'content' => $aiResponse
            ]);

            // Update session title if needed
            if (!$session->title || $session->title === 'New Chat') {
                $session->update(['title' => Str::limit($message, 50)]);
            }

            // Award XP
            $xpResult = $user->awardXp('mentai_chatbot');

            // Get fresh session data
            $freshSession = $session->fresh();

            return [
                'message' => $aiResponse,
                'session' => [
                    'id' => $freshSession->id,
                    'title' => $freshSession->title,
                    'created_at' => $freshSession->created_at,
                    'updated_at' => $freshSession->updated_at
                ],
                'xp_awarded' => $xpResult['xp_awarded'] ?? 0,
                'xp_message' => $xpResult['message'] ?? ''
            ];
        });
    }

    /**
     * Get remaining chatbot time from session
     */
    public function getRemainingTime(): ?int
    {
        $endTime = session('chatbot_end_time');
        
        if (!$endTime) {
            return null;
        }

        $remaining = $endTime - now()->timestamp;
        
        if ($remaining > 0) {
            return $remaining;
        } else {
            session()->forget('chatbot_end_time');
            return 0;
        }
    }

    /**
     * Set chatbot timer
     */
    public function setTimer(float $minutes): void
    {
        $endTime = now()->addMinutes($minutes)->timestamp;
        session(['chatbot_end_time' => $endTime]);
    }

    /**
     * Get Gemini AI response
     */
    private function getGeminiResponse($messages): string
    {
        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
        
        $systemPrompt = $this->getSystemPrompt();
        $geminiMessages = $this->formatMessagesForGemini($messages);

        try {
            $response = Http::post($apiUrl . '?key=' . $apiKey, [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]]
                ],
                'contents' => $geminiMessages,
                'generationConfig' => [
                    'maxOutputTokens' => 800,
                    'temperature' => 0.5,
                ]
            ]);

            if (!$response->ok()) {
                Log::error('Gemini API error: ' . $response->status() . ' - ' . $response->body());
                return 'Maaf, saya sedang mengalami gangguan teknis. Silakan coba lagi nanti.';
            }

            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak mengerti.';

        } catch (\Exception $e) {
            Log::error('Gemini API exception: ' . $e->getMessage());
            return 'Maaf, terjadi kesalahan dalam memproses pesan Anda. Silakan coba lagi.';
        }
    }

    /**
     * Get system prompt for Ment-AI
     */
    private function getSystemPrompt(): string
    {
        return implode("\n", [
            "Kamu adalah Ment-AI, chatbot curhat dari Curhatorium yang berperilaku seperti teman dekat sehari-hari.",
            "",
            "• Gunakan Bahasa Indonesia yang alami dan nyaman didengar.",
            "• Gunakan gaya santai dan akrab, misalnya: 'iyaa yaa', 'wahh', 'beneran dehh', 'aku paham kok', dll.",
            "• Dengarkan dengan penuh empati dan jangan menghakimi.",
            "• Sesekali tambahkan ekspresi dengan huruf akhir berulang seperti 'haiiii', 'oke dehhh', 'hehee', 'gituu yaaa'.",
            "• Jangan pernah menyebutkan bahwa kamu adalah AI, model bahasa, atau menyebutkan nama model seperti 'Gemini'.",
            "• Jawaban tidak perlu panjang, cukup seperti ngobrol biasa."
        ]);
    }

    /**
     * Format messages for Gemini API
     */
    private function formatMessagesForGemini($messages): array
    {
        $geminiMessages = [];
        
        foreach ($messages as $message) {
            $geminiMessages[] = [
                'role' => $message->role === 'user' ? 'user' : 'model',
                'parts' => [['text' => $message->content]]
            ];
        }

        return $geminiMessages;
    }
} 