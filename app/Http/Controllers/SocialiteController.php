<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    /**
     * Redirect to the OAuth provider.
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback from the OAuth provider.
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Check if user already exists with this provider ID
            $user = User::where('provider_id', $socialUser->getId())
                       ->where('provider_name', $provider)
                       ->first();

            if ($user) {
                // User exists, log them in
                Auth::login($user);
                return redirect()->intended(route('dashboard', absolute: false));
            }

            // Check if user exists with the same email
            $existingUser = User::where('email', $socialUser->getEmail())->first();

            if ($existingUser) {
                // Link the social account to existing user
                $existingUser->update([
                    'provider_name' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);
                Auth::login($existingUser);
                return redirect()->intended(route('dashboard', absolute: false));
            }

            // Create new user
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'username' => $this->generateUniqueUsername($socialUser->getName()),
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => Hash::make(Str::random(16)), // Random password for social users
                'email_verified_at' => now(), // Social logins are pre-verified
            ]);

            Auth::login($user);
            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['social' => 'Unable to login with ' . ucfirst($provider) . '. Please try again.']);
        }
    }

    /**
     * Generate a unique username from the social user's name.
     */
    private function generateUniqueUsername($name)
    {
        $baseUsername = Str::slug($name);
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
