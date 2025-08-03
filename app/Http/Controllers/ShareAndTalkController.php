<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ShareAndTalkService;
use App\Services\TicketService;
use App\Http\Requests\ChatMessageRequest;
use App\Models\Professional;
use App\Models\ChatSession;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShareAndTalkController extends Controller
{
    public function __construct(
        private ShareAndTalkService $shareAndTalkService,
        private TicketService $ticketService
    ) {}
    public function index() {
        return view('share-and-talk.index');
    }

    public function getProfessionals(Request $request)
    {
        $type = $request->query('type');
        $professionals = $this->shareAndTalkService->getProfessionals($type);
        
        return response()->json($professionals);
    }

    public function chatConsultation($professionalId) {
        try {
            $user = Auth::user();
            $session = $this->shareAndTalkService->createChatSession($professionalId, $user);
            
            return redirect()->route('share-and-talk.start-chat-session', ['sessionId' => $session->session_id]);
        } catch (\Exception $e) {
            Log::error('Chat consultation error: ' . $e->getMessage());
            return redirect()->route('share-and-talk')->with('error', 'An error occurred while creating the chat consultation. Please try again.');
        }
    }

    public function facilitatorChat($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            return redirect()->back()->withErrors(['msg' => 'Session not found.']);
        }
        
        // If this is a video session, redirect to the video facilitator
        if ($session->type === 'video') {
            return redirect()->route('share-and-talk.facilitator-video', ['sessionId' => $sessionId]);
        }
        
        $user = User::where('id', $session->user_id)->first();

        // Don't automatically activate the session - let the facilitator do it manually
        // The session should stay in 'waiting' status until explicitly activated

        $interval = now()->diffInMinutes($session->end);

        return view('share-and-talk.facilitator', [
            'sessionId' => $sessionId, 
            'professionalId' => $session->professional_id, 
            'user' => $user, 
            'interval' => $interval,
            'sessionStatus' => $session->status
        ]);
    }

    public function facilitatorVideo($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
    
        $interval = now()->diffInMinutes($session->end);

        return view('share-and-talk.facilitator-video', [
            'sessionId' => $sessionId, 
            'professionalId' => $session->professional_id,
            'interval' => $interval,
        ]);
    }

    public function userSend(ChatMessageRequest $request)
    {
        try {
            $user = Auth::user();
            $message = $this->shareAndTalkService->sendMessage(
                $request->session_id,
                $request->message,
                'user',
                $user->id
            );

            return response()->json([
                'status' => 'success',
                'data' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('User send message error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    public function facilitatorSend(ChatMessageRequest $request)
    {
        try {
            $session = ChatSession::where('session_id', $request->session_id)->first();
            
            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Session not found'
                ], 404);
            }

            $message = $this->shareAndTalkService->sendMessage(
                $request->session_id,
                $request->message,
                'professional',
                $session->professional_id
            );

            return response()->json([
                'status' => 'success',
                'data' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Facilitator send message error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    public function getMessages($sessionId) {
        $messages = $this->shareAndTalkService->getSessionMessages($sessionId);
        return response()->json($messages);
    }

    // API endpoint to get session status
    public function getSessionStatus($sessionId) {
        $status = $this->shareAndTalkService->getSessionStatus($sessionId);
        
        if (!$status) {
            return response()->json(['status' => 'not_found'], 404);
        }
        
        return response()->json($status);
    }

    // API endpoint to cancel a session by sessionId (for frontend timeout)
    public function cancelSessionByUser($sessionId) {
        $user = Auth::user();
        $success = $this->shareAndTalkService->cancelSessionByUser($sessionId, $user);
        
        if (!$success) {
            return response()->json(['status' => 'not_found_or_not_cancellable'], 404);
        }
        
        return response()->json(['status' => 'cancelled']);
    }

    // Cancel sessions that are still 'waiting' or 'pending' after timeout and return user's ticket
    public function cancelExpiredWaitingSessions()
    {
        $cancelledCount = $this->shareAndTalkService->cancelExpiredSessions();
        
        Log::info("Cancelled {$cancelledCount} expired sessions");
    }

    // API endpoint to set professional online (after session ends)
    public function setProfessionalOnline($professionalId) {
        $success = $this->shareAndTalkService->setProfessionalOnline($professionalId);
        
        if (!$success) {
            return response()->json(['status' => 'not_found'], 404);
        }
        
        return response()->json(['status' => 'online']);
    }

    public function videoConsultation($professionalId) {
        try {
            $user = Auth::user();
            $session = $this->shareAndTalkService->createVideoSession($professionalId, $user);
            
            return redirect()->route('share-and-talk.start-video-session', ['sessionId' => $session->session_id]);
        } catch (\Exception $e) {
            Log::error('Video consultation error: ' . $e->getMessage());
            return redirect()->route('share-and-talk')->with('error', $e->getMessage());
        }
    }
    


    public function cancelSession($sessionId) {
        try {
            $user = Auth::user();
            $success = $this->shareAndTalkService->cancelSession($sessionId, $user);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found or cannot be cancelled.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Session cancelled and ticket refunded successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Session cancellation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel session.'
            ], 500);
        }
    }

    public function endSession($sessionId) {
        try {
            $user = Auth::user();
            $result = $this->shareAndTalkService->endSession($sessionId, $user);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 404);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Session end error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to end session.'
            ], 500);
        }
    }



    // Route for professional to access the session (e.g., from WhatsApp link)
    public function activateSession($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        if (!$session) {
            return response('Session not found', 404);
        }
        
        // Don't automatically activate - just redirect to facilitator interface
        // The facilitator will manually activate the session when ready
        if ($session->status === 'waiting' || $session->status === 'pending') {
            // Redirect based on session type
            if ($session->type === 'chat') {
                return redirect()->route('share-and-talk.facilitator', ['sessionId' => $sessionId]);
            } else {
                // video session
                return redirect()->route('share-and-talk.facilitator-video', ['sessionId' => $sessionId]);
            }
        } elseif ($session->status === 'active') {
            // Already active, redirect based on session type
            if ($session->type === 'chat') {
                return redirect()->route('share-and-talk.facilitator', ['sessionId' => $sessionId]);
            } else {
                // video session
                return redirect()->route('share-and-talk.facilitator-video', ['sessionId' => $sessionId]);
            }
        } else {
            return response('Session is not in a state that can be activated.', 400);
        }
    }

    // First step: Validate session and redirect to video session
    public function startVideoSession($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            return redirect()->route('share-and-talk')->with('error', 'Session not found.');
        }
        
        // Check if the current user owns this session
        if ($session->user_id !== Auth::id()) {
            return redirect()->route('share-and-talk')->with('error', 'You are not authorized to access this session.');
        }
        
        // Check if session is still valid (not cancelled or completed)
        if (in_array($session->status, ['cancelled', 'completed'])) {
            return redirect()->route('share-and-talk')->with('error', 'This session has ended.');
        }
        
        // Redirect to the actual video session
        return redirect()->route('share-and-talk.video-session', ['sessionId' => $sessionId]);
    }

    // Second step: Display the video session (no session creation)
    public function userVideoSession($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            return redirect()->route('share-and-talk')->with('error', 'Session not found.');
        }
        
        // Check if the current user owns this session
        if ($session->user_id !== Auth::id()) {
            return redirect()->route('share-and-talk')->with('error', 'You are not authorized to access this session.');
        }
        
        // Check if session is still valid
        if (in_array($session->status, ['cancelled', 'completed'])) {
            return redirect()->route('share-and-talk')->with('error', 'This session has ended.');
        }
        
        $user = User::where('id', $session->user_id)->first();
        $professional = Professional::where('id', $session->professional_id)->first();
        
        if (!$user || !$professional) {
            return redirect()->route('share-and-talk')->with('error', 'Session data is invalid.');
        }
        
        $interval = now()->diffInMinutes($session->end);
        
        return view('share-and-talk.video', [
            'professional' => $professional, 
            'user' => $user, 
            'session_id' => $sessionId, 
            'interval' => $interval,
            'jitsi_room' => $session->jitsi_room,
            'user_display_name' => $user->name,
        ]);
    }

    // Manual activation endpoint for facilitators
    public function manualActivateSession($sessionId) {
        $success = $this->shareAndTalkService->activateSession($sessionId);
        
        if (!$success) {
            return response()->json(['error' => 'Session not found or cannot be activated'], 400);
        }
        
        return response()->json(['success' => true, 'message' => 'Session activated']);
    }

    // First step: Validate chat session and redirect to chat session
    public function startChatSession($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            return redirect()->route('share-and-talk')->with('error', 'Session not found.');
        }
        
        // Check if the current user owns this session
        if ($session->user_id !== Auth::id()) {
            return redirect()->route('share-and-talk')->with('error', 'You are not authorized to access this session.');
        }
        
        // Check if session is still valid (not cancelled or completed)
        if (in_array($session->status, ['cancelled', 'completed'])) {
            return redirect()->route('share-and-talk')->with('error', 'This session has ended.');
        }
        
        // Redirect to the actual chat session
        return redirect()->route('share-and-talk.chat-session', ['sessionId' => $sessionId]);
    }

    // Second step: Display the chat session (no session creation)
    public function userChatSession($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            return redirect()->route('share-and-talk')->with('error', 'Session not found.');
        }
        
        // Check if the current user owns this session
        if ($session->user_id !== Auth::id()) {
            return redirect()->route('share-and-talk')->with('error', 'You are not authorized to access this session.');
        }
        
        // Check if session is still valid
        if (in_array($session->status, ['cancelled', 'completed'])) {
            return redirect()->route('share-and-talk')->with('error', 'This session has ended.');
        }
        
        $user = User::where('id', $session->user_id)->first();
        $professional = Professional::where('id', $session->professional_id)->first();
        
        if (!$user || !$professional) {
            return redirect()->route('share-and-talk')->with('error', 'Session data is invalid.');
        }
        
        $interval = now()->diffInMinutes($session->end);
        
        return view('share-and-talk.chat', [
            'professional' => $professional, 
            'user' => $user, 
            'session_id' => $sessionId, 
            'interval' => $interval,
        ]);
    }




}
