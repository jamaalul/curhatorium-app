<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
        $professionals = $query->get()->map(function ($professional) {
            return [
                'id' => $professional->id,
                'name' => $professional->name,
                'title' => $professional->title,
                'avatar' => $professional->avatar,
                'specialties' => $professional->specialties,
                'type' => $professional->type,
                'rating' => $professional->rating,
                'status' => $professional->availability,
                'statusText' => $professional->availabilityText,
            ];
        });
        return response()->json($professionals);
    }

    public function chatConsultation($professionalId) {
        $professional = Professional::findOrFail($professionalId);
        $user = Auth::user();
        $session_id = Str::uuid();
        

        $session = ChatSession::create([
            'session_id' => $session_id,
            'user_id' => $user->id,
            'professional_id' => $professional->id,
            'start' => now('Asia/Jakarta'),
            'end' => now('Asia/Jakarta')->addMinutes(65), // 65 minutes
            'status' => 'waiting', // Set initial status
        ]);

        // Set professional to busy for 5 minutes
        $professional->availability = 'busy';
        $professional->availabilityText = 'Sedang menunggu konfirmasi sesi (5 menit)';
        $professional->save();

        $interval = now()->diffInMinutes($session->end);

        $response = Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN'),
        ])->post('https://api.fonnte.com/send', [
            'target' => $professional->whatsapp_number,
            'message' => 
                "Kamu mendapat pesanan konsultasi baru.\n" .
                "Akses URL berikut sebelum " . $session->start->addMinutes(5)->format('H:i') . " agar pesanannya tidak dibatalkan.\n\n" .
                "https://curhatorium.com/share-and-talk/facilitator/" . $session_id,
        ]);

        

        return view('share-and-talk.chat', ['professional' => $professional, 'user' => $user, 'session_id' => $session_id, 'interval' => $interval]);
    }

    public function facilitatorChat($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        $user = User::where('id', $session->user_id)->first();

        // If session is still waiting, activate it now
        if ($session->status === 'waiting') {
            $session->status = 'active';
            $session->start = now('Asia/Jakarta');
            $session->end = now('Asia/Jakarta')->addMinutes(65); // 65 minutes
            $session->save();
            // Set professional to busy for 65 minutes
            $professional = $session->professional;
            $professional->availability = 'busy';
            $professional->availabilityText = 'Sedang dalam sesi (65 menit)';
            $professional->save();
        }

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

    // API endpoint to get session status
    public function getSessionStatus($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        if (!$session) {
            return response()->json(['status' => 'not_found'], 404);
        }
        return response()->json(['status' => $session->status]);
    }

    // API endpoint to cancel a session by sessionId (for frontend timeout)
    public function cancelSessionByUser($sessionId) {
        $session = ChatSession::where('session_id', $sessionId)->first();
        if (!$session || $session->status !== 'waiting') {
            return response()->json(['status' => 'not_found_or_not_waiting'], 404);
        }
        $session->status = 'cancelled';
        $session->save();
        // Return ticket to user
        $user = $session->user;
        $type = $session->professional->type === 'psychiatrist' ? 'share_talk_psy_chat' : 'share_talk_ranger_chat';
        $ticket = $user->userTickets()->where('ticket_type', $type)->where(function($q) {
            $q->whereNull('limit_type')->orWhere('limit_type', '!=', 'unlimited');
        })->orderByDesc('expires_at')->first();
        if ($ticket && $ticket->remaining_value !== null) {
            $ticket->remaining_value += 1;
            $ticket->save();
        }
        // Set professional back to online
        $professional = $session->professional;
        $professional->availability = 'online';
        $professional->availabilityText = 'Tersedia';
        $professional->save();
        return response()->json(['status' => 'cancelled']);
    }

    // Cancel sessions that are still 'waiting' after 5 minutes and return user's ticket
    public function cancelExpiredWaitingSessions()
    {
        $expiredSessions = ChatSession::where('status', 'waiting')
            ->where('start', '<', now('Asia/Jakarta')->subMinutes(5))
            ->get();
        foreach ($expiredSessions as $session) {
            $session->status = 'cancelled';
            $session->save();
            // Return ticket to user
            $user = $session->user;
            $type = $session->professional->type === 'psychiatrist' ? 'share_talk_psy_chat' : 'share_talk_ranger_chat';
            $ticket = $user->userTickets()->where('ticket_type', $type)->where(function($q) {
                $q->whereNull('limit_type')->orWhere('limit_type', '!=', 'unlimited');
            })->orderByDesc('expires_at')->first();
            if ($ticket && $ticket->remaining_value !== null) {
                $ticket->remaining_value += 1;
                $ticket->save();
            }
        }
    }

    // API endpoint to set professional online (after session ends)
    public function setProfessionalOnline($professionalId) {
        $professional = Professional::find($professionalId);
        if ($professional) {
            $professional->availability = 'online';
            $professional->availabilityText = 'Tersedia';
            $professional->save();
            return response()->json(['status' => 'online']);
        }
        return response()->json(['status' => 'not_found'], 404);
    }
}
