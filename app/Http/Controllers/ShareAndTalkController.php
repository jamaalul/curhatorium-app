<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Professional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ChatSession;
use App\Models\Message;
use App\Models\User;
class ShareAndTalkController extends Controller
{
    public function index() {
        return view('share-and-talk.index');
    }

    public function getProfessionals(Request $request)
    {
        $type = $request->query('type');
        $query = Professional::query();
        if ($type) {
            $query->where('type', $type);
        }
        return response()->json($query->get());
    }

    public function chatConsultation($professionalId) {
        $professional = Professional::findOrFail($professionalId);
        $user = Auth::user();
        $session_id = Str::uuid();
        

        $session = ChatSession::create([
            'session_id' => $session_id,
            'user_id' => $user->id,
            'professional_id' => $professional->id,
            'start' => now(),
            'end' => now()->addMinutes(65),
        ]);

        $interval = now()->diffInMinutes($session->end);

        

        return view('share-and-talk.chat', ['professional' => $professional, 'user' => $user, 'session_id' => $session_id, 'interval' => $interval]);
    }

    public function facilitatorChat($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        $user = User::where('id', $session->user_id)->first();

        $interval = now()->diffInMinutes($session->end);

        return view('share-and-talk.facilitator', ['sessionId' => $sessionId, 'professionalId' => $session->professional_id, 'user' => $user, 'interval' => $interval]);
    }

    public function userSend(Request $request)
    {
        // try {
            $validated = $request->validate([
                'session_id' => 'required|string',
                'message' => 'required|string',
            ]);
    
            Message::create([
                'sender_id' => Auth::user()->id,
                'sender_type' => 'user',
                'session_id' => $validated['session_id'],
                'message' => $validated['message'],
            ]);
    
            // Process the message here
            return response()->json([
                'status' => 'success',
                'data' => $validated,
            ]);
    }

    public function facilitatorSend(Request $request)
    {
        // try {
            $validated = $request->validate([
                'session_id' => 'required|string',
                'message' => 'required|string',
            ]);
    
            // Get the chat session to find the professional_id
            $session = ChatSession::where('session_id', $validated['session_id'])->first();
            
            if (!$session) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Session not found'
                ], 404);
            }
    
            Message::create([
                'sender_id' => $session->professional_id,
                'sender_type' => 'professional',
                'session_id' => $validated['session_id'],
                'message' => $validated['message'],
            ]);
    
            // Process the message here
            return response()->json([
                'status' => 'success',
                'data' => $validated,
            ]);
    }

    public function getMessages($sessionId) {
        $messages = Message::where('session_id', $sessionId)->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }
}
