<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfessionalDashboardController extends Controller
{
    public function index($professionalId)
    {
        $professional = Professional::findOrFail($professionalId);
        
        return view('professional.dashboard', compact('professional'));
    }

    public function updateAvailability(Request $request, $professionalId)
    {
        $request->validate([
            'availability' => 'required|in:online,offline',
            'availabilityText' => 'required|string|max:255',
        ]);

        $professional = Professional::findOrFail($professionalId);
        
        $professional->update([
            'availability' => $request->availability,
            'availabilityText' => $request->availabilityText,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Availability updated successfully.',
            'professional' => $professional
        ]);
    }

    public function getAvailability($professionalId)
    {
        $professional = Professional::findOrFail($professionalId);
        
        return response()->json([
            'availability' => $professional->availability,
            'availabilityText' => $professional->availabilityText,
            'effectiveAvailability' => $professional->getEffectiveAvailability(),
            'effectiveAvailabilityText' => $professional->getEffectiveAvailabilityText(),
        ]);
    }

    public function dashboard($professionalId)
    {
        $professional = Professional::findOrFail($professionalId);
        
        // Get recent sessions for this professional
        $recentSessions = $professional->chatSessions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('professional.dashboard', compact('professional', 'recentSessions'));
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
} 