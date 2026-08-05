<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkCreateSlotRequest;
use App\Models\Professional;
use App\Models\ProfessionalScheduleSlot;
use App\Services\EntitlementService;
use App\Services\FonnteService;
use App\Services\RescheduleService;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfessionalDashboardController extends Controller
{
    public function __construct(
        private ScheduleService $scheduleService,
        private FonnteService $fonnteService,
        private RescheduleService $rescheduleService,
        private EntitlementService $entitlementService
    ) {}

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
            ->whereIn('status', ['booked', 'active'])
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->get();

        $consultationHistory = $professional->scheduleSlots()
            ->where('status', 'completed')
            ->whereNotNull('booked_by_user_id')
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->limit(6)
            ->get();

        return view('professional.dashboard', compact('professional', 'waitingConsultations', 'upcomingConsultations', 'consultationHistory'));
    }

    public function profile()
    {
        $professional = Auth::guard('professional')->user();

        $waitingConsultations = $professional->scheduleSlots()
            ->where('status', 'pending_confirmation')
            ->whereNotNull('booked_by_user_id')
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->get();

        $upcomingConsultations = $professional->scheduleSlots()
            ->whereIn('status', ['booked', 'active'])
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->get();

        $consultationHistory = $professional->scheduleSlots()
            ->where('status', 'completed')
            ->whereNotNull('booked_by_user_id')
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->limit(6)
            ->get();

        return view('professional.profile', compact('professional', 'waitingConsultations', 'upcomingConsultations', 'consultationHistory'));
    }

    public function consultationHistory()
    {
        $professional = Auth::guard('professional')->user();

        $waitingConsultations = $professional->scheduleSlots()
            ->where('status', 'pending_confirmation')
            ->whereNotNull('booked_by_user_id')
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->get();

        $upcomingConsultations = $professional->scheduleSlots()
            ->whereIn('status', ['booked', 'active'])
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->get();

        $consultationHistory = $professional->scheduleSlots()
            ->where('status', 'completed')
            ->whereNotNull('booked_by_user_id')
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->limit(6)
            ->get();

        $paginatedHistory = $professional->scheduleSlots()
            ->where('status', 'completed')
            ->whereNotNull('booked_by_user_id')
            ->with(['bookedBy', 'consultation'])
            ->orderBy('slot_start_time', 'desc')
            ->paginate(10);

        return view('professional.consultation-history', compact(
            'professional',
            'waitingConsultations',
            'upcomingConsultations',
            'consultationHistory',
            'paginatedHistory'
        ));
    }

    public function updateProfile(Request $request)
    {
        $professional = Auth::guard('professional')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'specialties' => 'nullable|string', // Will be comma separated
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('professionals/avatars', 'public');
            $validated['avatar'] = $path;
        }

        if (isset($validated['specialties'])) {
            $specialtiesArray = array_map('trim', explode(',', $validated['specialties']));
            // Filter out empty ones
            $validated['specialties'] = array_filter($specialtiesArray);
        }

        $professional->update($validated);

        return redirect()->route('professional.profile')->with('success', 'Profile updated successfully.');
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

        $result = $this->scheduleService->generateSlots(
            $professional,
            $validated['days'],
            $validated['start_time'],
            $validated['end_time'],
            $validated['start_date'],
            $validated['end_date'],
            $validated['conflict_resolution'] ?? 'skip'
        );

        $message = "Berhasil membuat {$result['created']} slot.";
        if ($result['skipped'] > 0) {
            $message .= " {$result['skipped']} slot dilewati karena konflik jadwal.";
        }

        return redirect()->route('professional.dashboard')
            ->with('success', $message);
    }

    public function getSchedule(Request $request, Professional $professional)
    {
        $isOwner = Auth::guard('professional')->check() && Auth::guard('professional')->id() == $professional->id;

        $slotsQuery = $professional->scheduleSlots()
            ->where('slot_start_time', '>=', now()->toDateTimeLocalString());

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
            $professional = Auth::guard('professional')->user();
            $sessionTime = Carbon::parse($slot->slot_start_time)->format('d M Y H:i');
            $reminderTimestamp = Carbon::parse($slot->slot_start_time)->subHour()->timestamp;

            // Confirmation message to client (sent immediately)
            $confirmationMessage = "Halo {$user->name},\n\n"
                .'Booking Anda pada tanggal '
                .Carbon::parse($slot->slot_start_time)->format('d M Y H:i')
                ." telah *dikonfirmasi*.\n\n"
                .'Terima kasih telah menggunakan layanan kami. '
                .'Sampai jumpa di waktu yang telah ditentukan!';
            $this->fonnteService->sendWhatsApp($consultation->no_wa, $confirmationMessage);

            // 1-hour reminder for the professional
            $professionalReminder = "Halo {$professional->name}, Anda memiliki sesi konsultasi dengan *{$user->username}* dalam 1 jam lagi.\n\n"
                ."Jadwal: {$sessionTime}\n\n"
                ."Silakan login ke dashboard untuk memulai:\n"
                .config('app.url').'/professional/dashboard';
            $this->fonnteService->sendScheduledWhatsApp($professional->whatsapp_number, $professionalReminder, $reminderTimestamp);

            // 1-hour reminder for the client
            $clientReminder = "Halo! Sesi konsultasi kamu dengan *{$professional->name}* dimulai dalam 1 jam lagi\n\n"
                ."Jadwal: {$sessionTime}\n\n"
                ."Jangan sampai terlambat ya. Sampai jumpa!\n"
                .config('app.url').'/share-and-talk';
            $this->fonnteService->sendScheduledWhatsApp($consultation->no_wa, $clientReminder, $reminderTimestamp);

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
                $benefitType = 'snt_psy_chat';
            } elseif ($consultation->consultation_type == 'Chat w/ Rangers') {
                $benefitType = 'snt_rgr_chat';
            } elseif ($consultation->consultation_type == 'Video Call w/ Psikolog') {
                $benefitType = 'snt_psy_vc';
            } else {
                $benefitType = null; // Unknown type
            }

            if ($benefitType) {
                $this->entitlementService->refundEntitlement($user, $benefitType);
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
}
