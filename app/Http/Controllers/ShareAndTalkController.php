<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ShareAndTalkService;
use App\Services\TicketService;
use App\Http\Requests\ChatMessageRequest;
use App\Models\Professional;
use App\Models\ChatSession;
use App\Models\Consultation;
use App\Models\Message;
use App\Models\MessageV2;
use App\Models\User;
use App\Models\ProfessionalScheduleSlot;
use App\Models\UserTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ShareAndTalkController extends Controller
{
    public function __construct(
        private ShareAndTalkService $shareAndTalkService,
        private TicketService $ticketService,
        private \App\Services\FonnteService $fonnteService
    ) {}
    public function index() {
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

    public function wait() {
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
        $validated = $request->validate([
            'professional_id' => 'required|integer|exists:professionals,id',
            'whatsapp_number' => 'required|string|max:20',
            'consultation_type' => 'required|string|in:chat,video',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
        ]);

        $slotStartTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        $user = Auth::user();

        $slot = DB::transaction(function () use ($validated, $slotStartTime, $user) {
            $slot = ProfessionalScheduleSlot::where('professional_id', $validated['professional_id'])
                ->where('slot_start_time', $slotStartTime)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();

            if (!$slot) {
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

            if (!$userTicket) {
                // This will trigger the transaction to rollback
                throw new \Exception('No valid ticket found for this consultation type.');
            }

            $userTicket->decrement('remaining_value');

            Consultation::create([
                'professional_schedule_slot_id' => $slot->id,
                'room' => 'sharetalk_' . uniqid(),
                'consultation_type' => $fullConsultationType,
                'no_wa' => $validated['whatsapp_number'],
            ]);

            return $slot;
        });

        if (!$slot) {
            return back()->with('error', 'Jadwal yang dipilih tidak lagi tersedia. Silakan pilih jadwal lain.');
        }

        $professional = $slot->professional;
        $message = "Halo {$professional->name}, Anda memiliki permintaan booking baru.\n\nSilakan cek dashboard Anda di:\n" . route('professional.login') . "\n\nTerima kasih.";
        $this->fonnteService->sendWhatsApp($professional->whatsapp_number, $message);

        return redirect()->route('share-and-talk.booked')->with('bookedSlot', $slot);
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
        if (!$bookedSlot) {
            // Redirect to dashboard if the session data is not available
            return redirect()->route('dashboard');
        }
        return view('share-and-talk.booked', compact('bookedSlot'));
    }

    public function chatRoom($room)
    {
        \Illuminate\Support\Facades\Log::info("Chat room access attempt for room: {$room}", [
            'is_professional' => Auth::guard('professional')->check(),
            'professional_id' => Auth::guard('professional')->id(),
            'is_user' => Auth::check(),
            'user_id' => Auth::id(),
            'request_uri' => request()->getRequestUri()
        ]);
        $roomExists = Consultation::where('room', $room)->exists();
        if (!$roomExists) {
            return redirect()->route('share-and-talk.index')->with('error', 'Ruang obrolan tidak ditemukan.');
        } else {
            $messages = MessageV2::where('room', $room)->orderBy('created_at', 'asc')->get();
            return view('share-and-talk.chat', ['room' => $room, 'messages' => $messages]);
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
            // Find the related professional schedule slot and update its status
            $slot = ProfessionalScheduleSlot::find($consultation->professional_schedule_slot_id);
            if ($slot) {
                $slot->status = 'completed';
                $slot->save();
            }
        }

        return redirect()->route('dashboard')->with('success', 'Sesi telah diakhiri.');
    }
}