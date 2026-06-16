<?php

namespace App\Http\Controllers\ShareAndTalk;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Professional;
use App\Models\ProfessionalScheduleSlot;
use App\Services\EntitlementService;
use App\Services\FonnteService;
use App\Services\ShareAndTalkService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct(
        private ShareAndTalkService $shareAndTalkService,
        private EntitlementService $entitlementService,
        private FonnteService $fonnteService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $upcomingConsultations = Consultation::where('user_id', $user->id)
            ->whereIn('status', ['waiting', 'pending', 'active'])
            ->where('start', '>=', now()->subHour(1))
            ->with(['professional', 'professionalScheduleSlot'])
            ->orderBy('start', 'asc')
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

        // Determine entitlement requirements
        $chatBenefitType = $professional->type === 'psychiatrist' ? 'snt_psy_chat' : 'snt_rgr_chat';
        $videoBenefitType = 'snt_psy_vc';

        $chatTickets = $this->entitlementService->getAvailableEntitlementAmount($user, $chatBenefitType);
        $videoTickets = $this->entitlementService->getAvailableEntitlementAmount($user, $videoBenefitType);

        $tickets = [
            'chat' => $chatTickets,
            'video' => $videoTickets,
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
            $professional = Professional::findOrFail($validated['professional_id']);

            // Determine benefit type
            $benefitType = '';
            $fullConsultationType = '';

            if ($validated['consultation_type'] === 'chat') {
                $benefitType = $professional->type === 'psychiatrist' ? 'snt_psy_chat' : 'snt_rgr_chat';
                $fullConsultationType = $professional->type === 'psychiatrist' ? 'Chat w/ Psikolog' : 'Chat w/ Rangers';
            } else {
                $benefitType = 'snt_psy_vc';
                $fullConsultationType = 'Video Call w/ Psikolog';
            }

            Log::info('Checkout attempt', [
                'user_id' => $user->id,
                'professional_id' => $professional->id,
                'benefit_type' => $benefitType,
                'slot_time' => $slotStartTime,
            ]);

            $consultation = DB::transaction(function () use ($validated, $slotStartTime, $user, $professional, $benefitType, $fullConsultationType) {
                // Find available slot
                $slot = ProfessionalScheduleSlot::where('professional_id', $professional->id)
                    ->where('slot_start_time', $slotStartTime)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if (! $slot) {
                    throw new \Exception('No available slot found.');
                }

                // Consume entitlement
                $consumed = $this->entitlementService->consumeEntitlement($user, $benefitType);

                if (! $consumed) {
                    throw new \Exception('No valid ticket found');
                }

                $slot->status = 'pending_confirmation';
                $slot->booked_by_user_id = $user->id;
                $slot->save();

                $room = 'sharetalk_'.uniqid().'_'.Str::random(5);

                return Consultation::create([
                    'professional_schedule_slot_id' => $slot->id,
                    'user_id' => $user->id,
                    'professional_id' => $professional->id,
                    'room' => $room,
                    'consultation_type' => $fullConsultationType,
                    'no_wa' => $validated['whatsapp_number'],
                    'status' => 'waiting',
                    'start' => $slotStartTime,
                    'end' => $slotStartTime->copy()->addMinutes(60),
                ]);
            });

            $message = "Halo {$professional->name}, Anda memiliki permintaan booking baru.\n\nSilakan cek dashboard Anda di:\n".route('professional.login')."\n\nTerima kasih.";
            $this->fonnteService->sendWhatsApp($professional->whatsapp_number, $message);

            // Re-load slot for view backwards compatibility if necessary
            $slot = ProfessionalScheduleSlot::find($consultation->professional_schedule_slot_id);
            $slot->load('professional');

            return redirect()->route('share-and-talk.booked')->with('bookedSlot', $slot);

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'No valid ticket found') !== false) {
                return back()->with('error', 'Tiket tidak cukup atau tidak valid untuk jenis konsultasi yang dipilih. Silakan periksa membership Anda.');
            }

            if (strpos($e->getMessage(), 'No available slot found') !== false) {
                return back()->with('error', 'Jadwal yang dipilih tidak tersedia. Silakan pilih jadwal lain.');
            }

            Log::error('Booking error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
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
            return redirect()->route('dashboard');
        }

        return view('share-and-talk.booked', compact('bookedSlot'));
    }
}
