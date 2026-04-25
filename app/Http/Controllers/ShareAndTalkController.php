<?php

namespace App\Http\Controllers;

use App\Events\SessionMessageSent;
use App\Events\StatusUpdated;
use App\Models\ChatSession;
use App\Models\Consultation;
use App\Models\Message;
use App\Models\MessageV2;
use App\Models\Professional;
use App\Models\ProfessionalScheduleSlot;
use App\Models\User;
use App\Models\UserTicket;
use App\Services\FonnteService;
use App\Services\ShareAndTalkService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShareAndTalkController extends Controller
{
    public function __construct(
        private ShareAndTalkService $shareAndTalkService,
        private TicketService $ticketService,
        private FonnteService $fonnteService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $upcomingConsultations = ProfessionalScheduleSlot::where('booked_by_user_id', $user->id)
            ->whereIn('status', ['pending_confirmation', 'booked'])
            ->where('slot_start_time', '>=', now()->subHour(1))
            ->with(['professional', 'consultation'])
            ->orderBy('slot_start_time', 'asc')
            ->get();

        return view('share-and-talk.index', compact('upcomingConsultations'));
    }

    public function getProfessionals(Request $request)
    {
        $type = $request->query('type');
        $date = $request->query('date');
        $professionals = $this->shareAndTalkService->getProfessionals($type, $date);

        return response()->json($professionals);
    }

    public function wait()
    {
        return view('share-and-talk.waiting');
    }

    public function showCheckoutPage(Professional $professional)
    {
        $user = Auth::user();
        $ticketSummary = $this->ticketService->getTicketSummary($user);

        // Corrected ticket type determination based on seeder and debug output
        $chatTicketType = $professional->type === 'psychiatrist' ? 'share_talk_psy_chat' : 'share_talk_ranger_chat';
        $videoTicketType = 'share_talk_psy_video';

        $tickets = [
            'chat' => $ticketSummary[$chatTicketType]['total_remaining'] ?? 0,
            'video' => $ticketSummary[$videoTicketType]['total_remaining'] ?? 0,
        ];

        return view('share-and-talk.checkout', compact('professional', 'tickets'));
    }

    public function bookSession(Request $request)
    {
        try {
            $validated = $request->validate([
                'professional_id' => 'required|integer|exists:professionals,id',
                'whatsapp_number' => 'required|string|max:20',
                'consultation_type' => 'required|string|in:chat,video',
                'date' => 'required|date_format:Y-m-d',
                'time' => 'required|date_format:H:i',
            ]);

            $slotStartTime = Carbon::parse($validated['date'].' '.$validated['time']);
            $user = Auth::user();

            // Debug: Get professional info and ticket summary
            $professional = Professional::find($validated['professional_id']);
            $ticketSummary = $this->ticketService->getTicketSummary($user);

            // Log debugging information
            Log::info('Checkout attempt', [
                'user_id' => $user->id,
                'professional_id' => $validated['professional_id'],
                'professional_type' => $professional->type ?? 'not found',
                'consultation_type' => $validated['consultation_type'],
                'slot_time' => $slotStartTime,
                'ticket_summary' => $ticketSummary,
            ]);

            $slot = DB::transaction(function () use ($validated, $slotStartTime, $user) {
                // Debug: Log all available slots for this professional on the requested date
                $availableSlots = ProfessionalScheduleSlot::where('professional_id', $validated['professional_id'])
                    ->where('status', 'available')
                    ->whereDate('slot_start_time', $validated['date'])
                    ->get();

                Log::info('Available slots debug', [
                    'professional_id' => $validated['professional_id'],
                    'requested_date' => $validated['date'],
                    'available_slots_count' => $availableSlots->count(),
                    'available_slot_times' => $availableSlots->pluck('slot_start_time')->toArray(),
                    'requested_time' => $validated['time'],
                    'combined_datetime' => $slotStartTime->toDateTimeString(),
                ]);

                $slot = ProfessionalScheduleSlot::where('professional_id', $validated['professional_id'])
                    ->where('slot_start_time', $slotStartTime)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if (! $slot) {
                    Log::warning('No available slot found', [
                        'professional_id' => $validated['professional_id'],
                        'slot_time' => $slotStartTime,
                    ]);

                    return null;
                }

                $slot->status = 'pending_confirmation';
                $slot->booked_by_user_id = $user->id;
                $slot->save();

                $slot->load('professional');

                // Determine the correct ticket type
                $professionalType = $slot->professional->type;
                $consultationType = $validated['consultation_type'];
                $ticketType = '';
                $fullConsultationType = '';

                if ($consultationType === 'chat') {
                    if ($professionalType === 'psychiatrist') {
                        $ticketType = 'share_talk_psy_chat';
                        $fullConsultationType = 'Chat w/ Psikolog';
                    } else {
                        $ticketType = 'share_talk_ranger_chat';
                        $fullConsultationType = 'Chat w/ Rangers';
                    }
                } elseif ($consultationType === 'video') {
                    // Assuming video is only for psychiatrists
                    $ticketType = 'share_talk_psy_video';
                    $fullConsultationType = 'Video Call w/ Psikolog';
                }

                // Find and update the user's ticket
                $userTicket = UserTicket::where('user_id', $user->id)
                    ->where('ticket_type', $ticketType)
                    ->where('expires_at', '>=', now())
                    ->where('remaining_value', '>=', 1)
                    ->orderBy('expires_at', 'desc')
                    ->lockForUpdate()
                    ->first();

                if (! $userTicket) {
                    // Log detailed ticket issue for debugging
                    $userTickets = UserTicket::where('user_id', $user->id)->get();
                    Log::warning('No valid ticket found', [
                        'user_id' => $user->id,
                        'required_ticket_type' => $ticketType,
                        'all_user_tickets' => $userTickets->toArray(),
                        'consultation_type' => $consultationType,
                        'professional_type' => $professionalType,
                    ]);
                    throw new \Exception('No valid ticket found for this consultation type.');
                }

                $userTicket->decrement('remaining_value');

                Consultation::create([
                    'professional_schedule_slot_id' => $slot->id,
                    'room' => 'sharetalk_'.uniqid(),
                    'consultation_type' => $fullConsultationType,
                    'no_wa' => $validated['whatsapp_number'],
                ]);

                return $slot;
            });

            if (! $slot) {
                return back()->with('error', json_encode($request));
            }

            $professional = $slot->professional;
            $message = "Halo {$professional->name}, Anda memiliki permintaan booking baru.\n\nSilakan cek dashboard Anda di:\n".route('professional.login')."\n\nTerima kasih.";
            $this->fonnteService->sendWhatsApp($professional->whatsapp_number, $message);

            return redirect()->route('share-and-talk.booked')->with('bookedSlot', $slot);
        } catch (\Exception $e) {
            // Handle ticket-related errors specifically
            if (strpos($e->getMessage(), 'No valid ticket found') !== false) {
                return back()->with('error', 'Tiket tidak cukup atau tidak valid untuk jenis konsultasi yang dipilih. Silakan periksa membership Anda.');
            }

            // Log any other errors
            Log::error('Booking error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi atau hubungi support.');
        }
    }

    public function getAvailabilitySlots(Request $request, Professional $professional)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $date = Carbon::parse($request->date);

        $slots = ProfessionalScheduleSlot::where('professional_id', $professional->id)
            ->whereDate('slot_start_time', $date)
            ->where('status', 'available')
            ->orderBy('slot_start_time')
            ->get()
            ->map(function ($slot) {
                return Carbon::parse($slot->slot_start_time)->format('H:i');
            });

        return response()->json($slots);
    }

    public function booked()
    {
        $bookedSlot = session('bookedSlot');
        if (! $bookedSlot) {
            // Redirect to dashboard if the session data is not available
            return redirect()->route('dashboard');
        }

        return view('share-and-talk.booked', compact('bookedSlot'));
    }

    public function chatRoom($room)
    {
        Log::info("Chat room access attempt for room: {$room}", [
            'is_professional' => Auth::guard('professional')->check(),
            'professional_id' => Auth::guard('professional')->id(),
            'is_user' => Auth::check(),
            'user_id' => Auth::id(),
            'request_uri' => request()->getRequestUri(),
        ]);
        $consultation = Consultation::where('room', $room)->first();

        if (! $consultation) {
            Log::warning('Room not found in chatRoom: '.$room);

            return redirect()->route('share-and-talk.index')->with('error', 'Ruang obrolan tidak ditemukan.');
        }

        // Update status to online for the current user (professional -> facilitator, otherwise client)
        $statusType = Auth::guard('professional')->check() ? 'facilitator' : 'client';
        $columnName = $statusType.'_status';
        $consultation->update([$columnName => 'online']);

        // Refresh the consultation to get the latest data
        $consultation->refresh();

        // Broadcast the status update
        StatusUpdated::dispatch($room, $statusType, 'online', $consultation);

        $messages = MessageV2::where('room', $room)->orderBy('created_at', 'asc')->get();

        return view('share-and-talk.chat', [
            'room' => $room,
            'messages' => $messages,
            'consultation' => $consultation,
        ]);
    }

    public function videoRoom($room)
    {
        Log::info("Video room access attempt for room: {$room}", [
            'is_professional' => Auth::guard('professional')->check(),
            'professional_id' => Auth::guard('professional')->id(),
            'is_user' => Auth::check(),
            'user_id' => Auth::id(),
            'request_uri' => request()->getRequestUri(),
        ]);

        $roomExists = Consultation::where('room', $room)->exists();
        if (! $roomExists) {
            return redirect()->route('share-and-talk.index')->with('error', 'Ruang obrolan tidak ditemukan.');
        } else {
            return view('share-and-talk.video', ['room' => $room]);
        }
    }

    public function endSession(Request $request)
    {
        $request->validate([
            'room' => 'required|string',
        ]);

        $room = $request->input('room');

        // Find the consultation by room
        $consultation = Consultation::where('room', $room)->first();

        if ($consultation) {
            // Update both statuses to offline when session ends
            $consultation->update([
                'facilitator_status' => 'offline',
                'client_status' => 'offline',
            ]);

            $statusType = Auth::guard('professional')->check() ? 'facilitator' : 'client';

            // Broadcast the status updates
            StatusUpdated::dispatch($room, $statusType, 'offline', $consultation);

            // Find the related professional schedule slot and update its status
            $slot = ProfessionalScheduleSlot::find($consultation->professional_schedule_slot_id);
            if ($slot) {
                $slot->status = 'completed';
                $slot->save();
            }
        }

        if (Auth::guard('professional')->check()) {
            $professionalId = Auth::guard('professional')->id();

            return redirect()->route('professional.dashboard', ['professionalId' => $professionalId])->with('success', 'Sesi telah diakhiri.');
        } else {
            return redirect()->route('dashboard')->with('success', 'Sesi telah diakhiri.');
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'room' => 'required|string',
            'status_type' => 'required|string|in:facilitator,client',
            'status' => 'required|string|in:online,offline',
        ]);

        $room = $request->input('room');
        $statusType = $request->input('status_type');
        $status = $request->input('status');

        // Find the consultation by room
        $consultation = Consultation::where('room', $room)->first();

        if (! $consultation) {
            Log::error("Room not found in updateStatus: {$room}");

            return response()->json(['status' => 'Room not found'], 404);
        }

        // Update the status
        $columnName = $statusType.'_status';
        $consultation->update([$columnName => $status]);

        // Reload the consultation with updated data
        $consultation->refresh();

        Log::info("Status updated for room: {$room}, type: {$statusType}, status: {$status}");

        // Broadcast the status update
        StatusUpdated::dispatch($room, $statusType, $status, $consultation);

        return response()->json(['status' => 'Status updated successfully']);
    }

    /**
     * Send a message from the facilitator (professional) and broadcast via Pusher.
     */
    public function facilitatorSend(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string|max:5000',
        ]);

        $sessionId = $request->input('session_id');
        $messageContent = $request->input('message');

        $session = ChatSession::where('session_id', $sessionId)->first();

        if (! $session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $professional = Auth::guard('professional')->user();

        if (! $professional) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $message = Message::create([
            'session_id' => $sessionId,
            'message' => $messageContent,
            'sender_type' => 'professional',
            'sender_id' => $professional->id,
        ]);

        // Activate the session on the first professional message if still waiting
        if (in_array($session->status, ['waiting', 'pending'])) {
            $session->update(['status' => 'active', 'start' => now(), 'end' => now()->addMinutes(65)]);
        }

        SessionMessageSent::dispatch($message, $sessionId);

        return response()->json(['status' => 'Message sent']);
    }

    /**
     * Return the current status of a ChatSession.
     */
    public function getSessionStatus(string $sessionId)
    {
        $session = ChatSession::where('session_id', $sessionId)->first();

        if (! $session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        return response()->json([
            'status' => $session->status,
            'start' => $session->start?->toIso8601String(),
            'end' => $session->end?->toIso8601String(),
        ]);
    }

    /**
     * Manually activate a ChatSession (facilitator presses "Mulai Sesi").
     */
    public function manualActivateSession(string $sessionId)
    {
        $session = ChatSession::where('session_id', $sessionId)->first();

        if (! $session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        if (! in_array($session->status, ['waiting', 'pending'])) {
            return response()->json(['error' => 'Session cannot be activated'], 422);
        }

        $session->update([
            'status' => 'active',
            'start' => now(),
            'end' => now()->addMinutes(65),
            'pending_end' => null,
        ]);

        return response()->json(['status' => 'Session activated']);
    }

    /**
     * Return all messages for a ChatSession (used by facilitator polling fallback).
     */
    public function getSessionMessages(string $sessionId)
    {
        $messages = Message::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($msg) => [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_type' => $msg->sender_type,
                'sender_id' => $msg->sender_id,
                'created_at' => $msg->created_at?->toIso8601String(),
            ]);

        return response()->json($messages);
    }
}
