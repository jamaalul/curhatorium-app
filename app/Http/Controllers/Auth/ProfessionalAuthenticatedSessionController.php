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
        $request->validate([
            'whatsapp_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        if (!Auth::guard('professional')->attempt($request->only('whatsapp_number', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'whatsapp_number' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        $professional = Auth::guard('professional')->user();

        return redirect()->intended(route('professional.dashboard', ['professionalId' => $professional->id]));
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

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('whatsapp_number')).'|'.$request->ip());
    }
}
