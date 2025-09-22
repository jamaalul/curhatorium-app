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
        try {
            $user = Auth::user();
            if (!$user) {
                Log::error('Chatbot getSessions: User not authenticated');
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $sessions = $this->chatbotService->getUserSessions($user);
            
            return response()->json($sessions);
        } catch (\Exception $e) {
            Log::error('Chatbot getSessions error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get sessions'], 500);
        }
    }

    public function getSession($sessionId) {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::error('Chatbot getSession: User not authenticated');
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            $session = $this->chatbotService->getSession($sessionId, $user);
            
            if (!$session) {
                Log::warning('Chatbot getSession: Session not found', ['session_id' => $sessionId, 'user_id' => $user->id]);
                return response()->json(['error' => 'Session not found'], 404);
            }
            
            return response()->json($session);
        } catch (\Exception $e) {
            Log::error('Chatbot getSession error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get session'], 500);
        }
    }

    public function createSession(Request $request) {
        try {
            $user = Auth::user();
            if (!$user) {
                Log::error('Chatbot createSession: User not authenticated');
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
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
