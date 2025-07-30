<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Check if this is user's first login (no stats recorded yet)
        $hasActivity = \App\Models\Stat::where('user_id', $user->id)->exists();
        if (!$hasActivity && !$user->onboarding_completed) {
            // This is likely their first login, keep onboarding_completed as false
            // so introjs will run
        }

        if ($user->is_admin) {
            return redirect('/admin');
        }

        return redirect('/dashboard'); // regular user
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function getUser()
    {
        $user = Auth::user();
        
        // Get daily XP summary for the navbar progress indicator
        $dailyXpSummary = $user->getDailyXpSummary();
        
        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'total_xp' => $user->total_xp,
            'daily_xp_summary' => $dailyXpSummary
        ]);
    }
}
