<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkCreateSlotRequest;
use App\Models\Professional;
use App\Models\ProfessionalScheduleSlot;
use App\Models\Reschedule;
use App\Models\UserTicket;
use App\Services\FonnteService;
use App\Services\RescheduleService;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfessionalDashboardController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private FonnteService $fonnteService,
        private RescheduleService $rescheduleService
    ) {}

    public function index()
    {
        $professional = Auth::guard('professional')->user();

        return view('professional.dashboard', compact('professional'));
    }

    public function dashboard()
    {
        $professional = Auth::guard('professional')->user();

        $waitingConsultations = $professional->scheduleSlots()
            ->where('status', 'pending_confirmation')
            ->whereNotNull('booked_by_user_id')
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->get();

        $upcomingConsultations = $professional->scheduleSlots()
            ->where('status', 'booked')
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->get();

        return view('professional.dashboard', compact('professional', 'waitingConsultations', 'upcomingConsultations'));
    }

    public function logout(Request $request)
    {
        Auth::guard('professional')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('professional.login');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ]);

        $professional = Auth::guard('professional')->user();

        // Verify current password
        if (! Hash::check($request->current_password, $professional->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        // Update password
        $professional->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    public function setAvailability(BulkCreateSlotRequest $request)
    {
        $validated = $request->validated();
        $professional = Auth::guard('professional')->user();

        $this->scheduleService->generateSlots(
            $professional,
            $validated['days'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['start_date'],
            $validated['end_date']
        );

        return redirect()->route('professional.dashboard')
            ->with('success', 'Slot berhasil dibuat.');
    }

    public function getSchedule(Request $request, Professional $professional)
    {
        $isOwner = Auth::guard('professional')->check() && Auth::guard('professional')->id() == $professional->id;

        $slotsQuery = $professional->scheduleSlots()
            ->where('slot_start_time', '>=', $request->start)
            ->where('slot_end_time', '>=', now()->toDateTimeLocalString());

        // If the viewer is not the owner, only show available slots

        $slots = $slotsQuery->get();

        $events = $slots->map(function ($slot) use ($isOwner) {
            $status = $slot->status;
            $color = '#10b981'; // Green for available
            $title = 'Available';

            if ($isOwner) {
                if ($status === 'booked') {
                    $color = '#ef4444'; // Red for booked
                    $title = 'Booked';
                } elseif ($status === 'pending_confirmation') {
                    $color = '#f59e0b'; // Yellow for pending
                    $title = 'Pending';
                } elseif ($status === 'completed') {
                    $color = '#6b7280'; // Gray for completed
                    $title = 'Completed';
                }
            } else {
                // For non-owners, both booked and completed slots are just unavailable
                if ($status === 'booked' || $status === 'pending_confirmation' || $status === 'completed') {
                    $color = '#d1d5db'; // A generic "unavailable" color
                    $title = 'Not Available';
                }
            }

            return [
                'id' => $slot->id,
                'title' => $title,
                'start' => $slot->slot_start_time,
                'end' => $slot->slot_end_time,
                'color' => $color,
                'allDay' => false,
            ];
        });

        return response()->json($events);
    }

    public function acceptBooking(ProfessionalScheduleSlot $slot)
    {
        // Authorization check
        if ($slot->professional_id !== Auth::guard('professional')->id()) {
            abort(403);
        }

        if ($slot->status === 'pending_confirmation') {
            $slot->status = 'booked';
            $slot->save();
            $user = $slot->bookedBy;
            $consultation = $slot->consultation;
            $message = "Halo {$user->name},\n\n"
                .'Booking Anda pada tanggal '
                .Carbon::parse($slot->slot_start_time)->format('d M Y H:i')
                ." telah *dikonfirmasi*.\n\n"
                .'Terima kasih telah menggunakan layanan kami. '
                .'Sampai jumpa di waktu yang telah ditentukan!';
            $this->fonnteService->sendWhatsApp($consultation->no_wa, $message);

            return back()->with('success', 'Booking accepted.');
        }

        return back()->with('error', 'This booking is not pending confirmation.');
    }

    public function declineBooking(ProfessionalScheduleSlot $slot)
    {
        // Authorization check
        if ($slot->professional_id !== Auth::guard('professional')->id()) {
            abort(403);
        }

        if ($slot->status === 'pending_confirmation') {
            $user = $slot->bookedBy;
            $consultation = $slot->consultation;
            if ($consultation->consultation_type == 'Chat w/ Psikolog') {
                $ticketType = 'share_talk_psy_chat';
            } elseif ($consultation->consultation_type == 'Chat w/ Rangers') {
                $ticketType = 'share_talk_ranger_chat';
            } elseif ($consultation->consultation_type == 'Video Call w/ Psikolog') {
                $ticketType = 'share_talk_psy_video';
            } else {
                $ticketType = null; // Unknown type
            }

            // Find and update the user's ticket
            $userTicket = UserTicket::where('user_id', $user->id)
                ->where('ticket_type', $ticketType)
                ->where('expires_at', '>=', now())
                ->orderBy('expires_at', 'desc')
                ->lockForUpdate()
                ->first();

            if ($userTicket) {
                $userTicket->increment('remaining_value');
            } else {
                // If no ticket is found, create a new one.
                // This might happen if the ticket was 'unlimited' and not stored,
                // or if it expired between booking and cancellation.
                // Adjust the logic as per your application's rules.
                dd($consultation->consultation_type, $slot->professional->type);
            }

            $slot->status = 'available';
            $slot->booked_by_user_id = null;
            $slot->save();

            $message = "Halo {$user->name},\n\n"
                .'Mohon maaf, booking Anda pada tanggal '
                .Carbon::parse($slot->slot_start_time)->format('d M Y H:i')
                ." tidak dapat kami konfirmasi.\n\n"
                ."Namun, jangan khawatir. Tiket Anda sudah kami kembalikan.\n"
                ."Silahkan buat booking dengan jadwal yang lain.\n\n"
                .'Terima kasih atas pengertian Anda.';
            $this->fonnteService->sendWhatsApp($consultation->no_wa, $message);

            return back()->with('success', 'Booking declined and ticket refunded.');
        }

        return back()->with('error', 'This booking is not pending confirmation.');
    }

    public function deleteSlot(ProfessionalScheduleSlot $slot)
    {
        // Authorization check
        if ($slot->professional_id !== Auth::guard('professional')->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Only allow deleting available slots
        if ($slot->status !== 'available') {
            return response()->json(['success' => false, 'message' => 'You can only delete available slots.'], 422);
        }

        $slot->delete();

        return response()->json(['success' => true, 'message' => 'Slot deleted successfully.']);
    }

    /**
     * Start the reschedule process for a pending booking
     *
     * @return Response
     */
    public function rescheduleBooking(ProfessionalScheduleSlot $slot)
    {
        // Authorization check
        if ($slot->professional_id !== Auth::guard('professional')->id()) {
            abort(403);
        }

        // Only allow rescheduling of pending or booked slots
        if (! in_array($slot->status, ['pending_confirmation', 'booked'])) {
            return back()->with('error', 'This booking cannot be rescheduled.');
        }

        // Create a reschedule record
        $reschedule = $this->rescheduleService->createRescheduleOffer($slot, [], null);

        // Redirect to the form for selecting available slots
        return redirect()->route('professional.reschedule.offer-slots', [
            'rescheduleId' => $reschedule->id,
        ])->with('reschedule_id', $reschedule->id);
    }

    /**
     * Show the form for selecting available slots to offer
     *
     * @param  int  $professionalId
     * @param  int  $rescheduleId
     * @return Response
     */
    public function showOfferSlotsForm($rescheduleId)
    {
        // Authorization check
        $professional = Auth::guard('professional')->user();

        // Get the reschedule
        $reschedule = Reschedule::findOrFail($rescheduleId);
        $originalSlot = $reschedule->originalSlot;

        // Get available slots in the next 30 days (excluding the original slot)
        $availableSlots = $professional->scheduleSlots()
            ->where('status', 'available')
            ->where('slot_start_time', '>=', now())
            ->where('slot_end_time', '<=', now()->addDays(30))
            ->where('id', '!=', $originalSlot->id)
            ->orderBy('slot_start_time', 'asc')
            ->get();

        return view('professional.reschedule.offer-slots', [
            'professional' => $professional,
            'reschedule' => $reschedule,
            'originalSlot' => $originalSlot,
            'availableSlots' => $availableSlots,
        ]);
    }

    /**
     * Save the offered slots and send notification to client
     *
     * @param  int  $professionalId
     * @param  int  $rescheduleId
     * @return Response
     */
    public function offerRescheduleSlots(Request $request, $rescheduleId)
    {
        // Authorization check
        $professional = Auth::guard('professional')->user();

        // Validate the request
        $request->validate([
            'notes' => 'nullable|string|max:500',
            'slots' => 'required|array|min:1',
            'slots.*' => 'exists:professional_schedule_slots,id',
        ]);

        // Get the reschedule
        $reschedule = Reschedule::findOrFail($rescheduleId);
        $originalSlot = $reschedule->originalSlot;
        $selectedSlots = $request->input('slots');
        $notes = $request->input('notes');

        // Update the reschedule with the offered slots
        $this->rescheduleService->updateRescheduleOffer($reschedule, $selectedSlots, $notes);

        // Send WhatsApp notification to client
        $consultation = $reschedule->consultation;
        $client = $originalSlot->bookedBy;
        $originalDate = Carbon::parse($originalSlot->slot_start_time)->format('d M Y');
        $originalTime = Carbon::parse($originalSlot->slot_start_time)->format('H:i');

        // Generate the reschedule link
        $rescheduleLink = route('reschedule.client', $reschedule->token);

        $message = "Halo {$client->name},\n\n"
            ."{$professional->name} ingin menukar jadwal konsultasi Anda.\n\n"
            ."🗓️ Jadwal Lama: {$originalDate}\n"
            ."🕐 Waktu: {$originalTime}\n\n"
            ."Silakan pilih jadwal baru melalui link berikut:\n"
            ."{$rescheduleLink}\n\n"
            ."Pilihan harus dilakukan dalam 48 jam.\n\n"
            ."Terima kasih,\n"
            .'Tim Curhatorium';

        $this->fonnteService->sendWhatsApp($consultation->no_wa, $message);

        return redirect()->route('professional.dashboard')
            ->with('success', 'Reschedule offer sent to client successfully.');
    }

    /**
     * List all reschedules for the professional
     *
     * @param  int  $professionalId
     * @return Response
     */
    public function listReschedules()
    {
        // Authorization check
        $professional = Auth::guard('professional')->user();
        $professionalId = $professional->id;

        // Get all reschedules for this professional's bookings
        $reschedules = Reschedule::whereHas('originalSlot', function ($query) use ($professionalId) {
            $query->where('professional_id', $professionalId);
        })
            ->with(['rescheduleSlots.slot', 'originalSlot', 'consultation'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('professional.reschedule.list', [
            'professional' => $professional,
            'reschedules' => $reschedules,
        ]);
    }
}
