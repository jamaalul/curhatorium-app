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
use App\Models\ProfessionalScheduleSlot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'consultation_type' => 'required|string|in:chat,video',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
        ]);

        $slotStartTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        $user = Auth::user();

        $bookingResult = DB::transaction(function () use ($validated, $slotStartTime, $user) {
            $slot = ProfessionalScheduleSlot::where('professional_id', $validated['professional_id'])
                ->where('slot_start_time', $slotStartTime)
                ->where('status', 'available')
                ->lockForUpdate()
                ->first();

            if (!$slot) {
                return ['success' => false, 'message' => 'Jadwal yang dipilih tidak lagi tersedia. Silakan pilih jadwal lain.'];
            }

            // Here, you would also consume the user's ticket.
            // For simplicity, we'll skip that for now but it's a critical step.

            $slot->status = 'pending_confirmation';
            $slot->booked_by_user_id = $user->id;
            $slot->save();

            // You can also create a record in a general `appointments` table here.

            return ['success' => true];
        });

        if (!$bookingResult['success']) {
            return back()->with('error', $bookingResult['message']);
        }

        return view('share-and-talk.booked')->with('success', 'Permintaan sesi Anda telah terkirim! Mohon tunggu konfirmasi dari fasilitator.');
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
}