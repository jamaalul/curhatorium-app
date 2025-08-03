<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatbotService;
use App\Http\Requests\ChatbotMessageRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function __construct(
        private ChatbotService $chatbotService
    ) {}
    public function index(Request $request) {
        $user = Auth::user();
        $sessions = $this->chatbotService->getUserSessions($user);
        $chatbotSecondsLeft = $this->chatbotService->getRemainingTime();

        if ($request->has('consume_amount')) {
            $minutes = floatval($request->input('consume_amount'));
            $this->chatbotService->setTimer($minutes);
            return redirect()->route('chatbot');
        }

        return view('chatbot', compact('sessions', 'chatbotSecondsLeft'));
    }

    public function getSessions() {
        $user = Auth::user();
        $sessions = $this->chatbotService->getUserSessions($user);
        
        return response()->json($sessions);
    }

    public function getSession($sessionId) {
        $user = Auth::user();
        $session = $this->chatbotService->getSession($sessionId, $user);
        
        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }
        
        return response()->json($session);
    }

    public function createSession(Request $request) {
        try {
            $user = Auth::user();
            $title = $request->input('title', 'New Chat');
            $session = $this->chatbotService->createSession($user, $title);
            
            return response()->json($session);
        } catch (\Exception $e) {
            Log::error('Create chatbot session error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create session'], 500);
        }
    }

    public function deleteSession($sessionId) {
        try {
            $user = Auth::user();
            $success = $this->chatbotService->deleteSession($sessionId, $user);
            
            if (!$success) {
                return response()->json(['error' => 'Session not found'], 404);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Delete chatbot session error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete session'], 500);
        }
    }

    public function chat(ChatbotMessageRequest $request)
    {
        try {
            $user = Auth::user();
            $result = $this->chatbotService->processChatMessage(
                $request->session_id,
                $request->message,
                $user
            );
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Chatbot chat error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
