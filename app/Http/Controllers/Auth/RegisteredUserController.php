<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
        ]);

        // Grant Calm Starter membership and tickets immediately upon registration
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $calmStarter = \App\Models\Membership::where('name', 'Calm Starter')->first();
        if ($calmStarter) {
            $hasActive = \App\Models\UserMembership::where('user_id', $user->id)
                ->where('membership_id', $calmStarter->id)
                ->where('started_at', '>=', $monthStart)
                ->where('expires_at', '<=', $monthEnd)
                ->exists();
            if (!$hasActive) {
                $expires = $monthEnd->copy()->endOfDay();
                $userMembership = \App\Models\UserMembership::create([
                    'user_id' => $user->id,
                    'membership_id' => $calmStarter->id,
                    'started_at' => $now,
                    'expires_at' => $expires,
                ]);
                $tickets = \App\Models\MembershipTicket::where('membership_id', $calmStarter->id)->get();
                foreach ($tickets as $ticket) {
                    $isUnlimited = $ticket->limit_type === 'unlimited';
                    $value = $isUnlimited ? null : $ticket->limit_value;
                    \App\Models\UserTicket::create([
                        'user_id' => $user->id,
                        'ticket_type' => $ticket->ticket_type,
                        'limit_type' => $ticket->limit_type,
                        'limit_value' => $isUnlimited ? null : $value,
                        'remaining_value' => $isUnlimited ? null : $value,
                        'expires_at' => $expires,
                    ]);
                }
            }
        }

        event(new Registered($user));

        // Auth::login($user);

        return redirect(route('privacy-policy', absolute: false));
    }
}
