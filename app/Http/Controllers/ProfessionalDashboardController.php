<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\ProfessionalScheduleSlot;
use App\Services\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfessionalDashboardController extends Controller
{
    public function __construct(private ScheduleService $scheduleService)
    {
    }

    public function index($professionalId)
    {
        $professional = Professional::findOrFail($professionalId);
        if (Auth::guard('professional')->id() != $professionalId) {
            abort(403);
        }
        
        return view('professional.dashboard', compact('professional'));
    }


    public function dashboard($professionalId)
    {
        $professional = Professional::findOrFail($professionalId);
        if (Auth::guard('professional')->id() != $professionalId) {
            abort(403);
        }
        
        // Get recent sessions for this professional
        $recentSessions = $professional->scheduleSlots()
            ->whereIn('status', ['booked', 'pending_confirmation', 'completed'])
            ->whereNotNull('booked_by_user_id')
            ->with('bookedBy')
            ->orderBy('slot_start_time', 'desc')
            ->limit(10)
            ->get();

        $pendingBookings = $professional->scheduleSlots()
            ->where('status', 'pending_confirmation')
            ->with('bookedBy')
            ->orderBy('slot_start_time', 'asc')
            ->get();
        
        return view('professional.dashboard', compact('professional', 'recentSessions', 'pendingBookings'));
    }

    public function logout(Request $request)
    {
        Auth::guard('professional')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('professional.login');
    }

    public function changePassword(Request $request, $professionalId)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ]);

        $professional = Professional::findOrFail($professionalId);
        if (Auth::guard('professional')->id() != $professionalId) {
            abort(403);
        }
        
        // Verify current password
        if (!Hash::check($request->current_password, $professional->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        // Update password
        $professional->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    public function setAvailability(Request $request)
    {
        $request->validate([
            'days' => 'required|array',
            'days.*' => 'integer|between:0,6', // 0 for Sunday, 6 for Saturday
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $professional = Auth::guard('professional')->user();

        $this->scheduleService->generateSlots(
            $professional,
            $request->days,
            $request->start_time,
            $request->end_time,
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'success' => true,
            'message' => 'Availability set successfully.'
        ]);
    }

    public function getSchedule(Request $request, Professional $professional)
    {
        if (Auth::guard('professional')->id() != $professional->id) {
            abort(403);
        }

        $slots = $professional->scheduleSlots()
            ->where('slot_start_time', '>=', $request->start)
            ->where('slot_end_time', '<=', $request->end)
            ->get();

        $events = $slots->map(function ($slot) {
            $status = $slot->status;
            $color = '#6b7280'; // Default to gray
            if ($status === 'available') {
                $color = '#10b981'; // Green
            } elseif ($status === 'booked') {
                $color = '#ef4444'; // Red
            } elseif ($status === 'pending_confirmation') {
                $color = '#f59e0b'; // Yellow
            }

            return [
                'title' => ucfirst($status),
                'start' => $slot->slot_start_time,
                'end' => $slot->slot_end_time,
                'color' => $color,
                'allDay' => false
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
            // Here you would also send a notification to the user.
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
            $slot->status = 'available';
            $slot->booked_by_user_id = null;
            $slot->save();
            // Here you would also send a notification to the user.
            return back()->with('success', 'Booking declined.');
        }

        return back()->with('error', 'This booking is not pending confirmation.');
    }
}