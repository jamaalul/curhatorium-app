<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ProfessionalAuthenticatedSessionController extends Controller
{
    /**
     * Display the professional login view.
     */
    public function create(): View
    {
        return view('auth.professional-login');
    }

    /**
     * Handle an incoming professional authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'whatsapp_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('professional')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $professional = Auth::guard('professional')->user();
            return redirect()->route('professional.dashboard', ['professionalId' => $professional->id]);
        }

        throw ValidationException::withMessages([
            'whatsapp_number' => trans('auth.failed'),
        ]);
    }

    /**
     * Destroy an authenticated professional session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('professional')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('professional.login');
    }

}
