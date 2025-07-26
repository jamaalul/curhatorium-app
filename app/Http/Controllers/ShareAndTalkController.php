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
        // Return ticket to user based on session type
        $user = $session->user;
        
        if ($session->type === 'video') {
            // Video session - refund video ticket
            $ticketType = 'share_talk_psy_video';
        } else {
            // Chat session - determine ticket type based on professional type
            $ticketType = $session->professional->type === 'psychiatrist' ? 'share_talk_psy_chat' : 'share_talk_ranger_chat';
        }
        
        $ticket = $user->userTickets()->where('ticket_type', $ticketType)->where(function($q) {
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
            // Return ticket to user based on session type
            $user = $session->user;
            
            if ($session->type === 'video') {
                // Video session - refund video ticket
                $ticketType = 'share_talk_psy_video';
            } else {
                // Chat session - determine ticket type based on professional type
                $ticketType = $session->professional->type === 'psychiatrist' ? 'share_talk_psy_chat' : 'share_talk_ranger_chat';
            }
            
            $ticket = $user->userTickets()->where('ticket_type', $ticketType)->where(function($q) {
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

    public function videoConsultation($professionalId) {
        try {
            $professional = Professional::findOrFail($professionalId);
            $user = Auth::user();
            
            // Validate that the professional is a psychiatrist
            if ($professional->type !== 'psychiatrist') {
                return redirect()->route('share-and-talk')->with('error', 'Video consultations are only available with psychiatrists.');
            }
            
            // Check if professional is available
            if ($professional->availability !== 'online') {
                return redirect()->route('share-and-talk')->with('error', 'This professional is currently not available for video consultations.');
            }
            
            $session_id = Str::uuid();
            
            // Create video session
            $session = ChatSession::create([
                'session_id' => $session_id,
                'user_id' => $user->id,
                'professional_id' => $professional->id,
                'start' => now('Asia/Jakarta'),
                'end' => now('Asia/Jakarta')->addMinutes(65), // 65 minutes
                'status' => 'waiting',
                'type' => 'video', // Add type to distinguish video sessions
            ]);
    
            // Set professional to busy
            $professional->availability = 'busy';
            $professional->availabilityText = 'Sedang menunggu konfirmasi sesi video (5 menit)';
            $professional->save();
    
            $interval = now()->diffInMinutes($session->end);
    
            // Create Google Meet link
            $meetLink = $this->createGoogleMeetLink($session_id, $professional, $user);
    
            // Send WhatsApp notification to professional
            try {
                $response = Http::withHeaders([
                    'Authorization' => env('FONNTE_TOKEN'),
                ])->post('https://api.fonnte.com/send', [
                    'target' => $professional->whatsapp_number,
                    'message' => 
                        "Kamu mendapat pesanan konsultasi VIDEO baru.\n" .
                        "Akses URL berikut sebelum " . $session->start->addMinutes(5)->format('H:i') . " agar pesanannya tidak dibatalkan.\n\n" .
                        "Google Meet: " . $meetLink . "\n\n" .
                        "Dashboard: https://curhatorium.com/share-and-talk/facilitator/" . $session_id,
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the session creation
                \Log::error('Failed to send WhatsApp notification: ' . $e->getMessage());
            }
    
            return view('share-and-talk.video', [
                'professional' => $professional, 
                'user' => $user, 
                'session_id' => $session_id, 
                'interval' => $interval,
                'meet_link' => $meetLink
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('share-and-talk')->with('error', 'Professional not found.');
        } catch (\Exception $e) {
            \Log::error('Video consultation error: ' . $e->getMessage());
            return redirect()->route('share-and-talk')->with('error', 'An error occurred while creating the video consultation. Please try again.');
        }
    }
    
    private function createGoogleMeetLink($sessionId, $professional, $user) {
        // For now, create a simple Google Meet link
        // In production, you'd use Google Calendar API to create actual meetings
        $meetingId = Str::random(10);
        return "https://meet.google.com/" . $meetingId;
        
        // TODO: Implement Google Calendar API integration
        // This would create a proper calendar event with Meet link
        // and send invites to both parties
    }

    public function cancelSession($sessionId) {
        try {
            $session = ChatSession::where('session_id', $sessionId)
                ->where('user_id', Auth::id())
                ->where('status', 'waiting')
                ->firstOrFail();

            // Update session status to cancelled
            $session->status = 'cancelled';
            $session->save();

            // Reset professional availability
            $professional = $session->professional;
            $professional->availability = 'online';
            $professional->availabilityText = 'Tersedia';
            $professional->save();

            // Refund the ticket based on session type
            $ticketType = $session->type === 'video' ? 'share_talk_psy_video' : 'share_talk_psy_chat';
            $this->refundTicket(Auth::user(), $ticketType);

            return response()->json([
                'success' => true,
                'message' => 'Session cancelled and ticket refunded successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Session cancellation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel session.'
            ], 500);
        }
    }

    public function endSession($sessionId) {
        try {
            $session = ChatSession::where('session_id', $sessionId)
                ->where('user_id', Auth::id())
                ->where('status', 'active')
                ->firstOrFail();

            // Update session status to completed
            $session->status = 'completed';
            $session->end = now('Asia/Jakarta');
            $session->save();

            // Reset professional availability
            $professional = $session->professional;
            $professional->availability = 'online';
            $professional->availabilityText = 'Tersedia';
            $professional->save();

            return response()->json([
                'success' => true,
                'message' => 'Session ended successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Session end error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to end session.'
            ], 500);
        }
    }

    private function refundTicket($user, $ticketType) {
        // Find the most recently used ticket for this type
        $ticket = $user->userTickets()
            ->where('ticket_type', $ticketType)
            ->where(function($q) {
                $q->where('limit_type', 'hour')
                  ->orWhere('limit_type', 'count');
            })
            ->where('remaining_value', '<', 1.0) // Assuming 1 unit was consumed
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($ticket) {
            // Refund 1 unit back to the ticket (1 hour for hour-based, 1 count for count-based)
            $refundAmount = $ticket->limit_type === 'hour' ? 1.0 : 1;
            $ticket->remaining_value += $refundAmount;
            $ticket->save();
            
            \Log::info("Ticket refunded for user {$user->id}, ticket type: {$ticketType}, refund amount: {$refundAmount}");
        } else {
            \Log::warning("No ticket found to refund for user {$user->id}, ticket type: {$ticketType}");
        }
    }


}
