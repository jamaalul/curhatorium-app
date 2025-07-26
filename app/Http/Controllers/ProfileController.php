<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        // Group tickets by type and aggregate info for each type, excluding tickets with 0 values
        $tickets = $user->userTickets
            ->filter(function ($ticket) {
                // Exclude tickets with 0 limit_value
                // For remaining_value: exclude 0, but allow null (unlimited tickets)
                return $ticket->limit_value !== 0 && 
                       ($ticket->remaining_value === null || $ticket->remaining_value > 0);
            })
            ->groupBy('ticket_type')
            ->map(function ($group) {
                $first = $group->first();
                $allUnlimited = $group->every(function ($t) {
                    return $t->limit_type === 'unlimited' || is_null($t->remaining_value);
                });
                return [
                    'ticket_type' => $first->ticket_type,
                    'limit_type' => $first->limit_type,
                    'remaining_value' => $allUnlimited ? null : $group->sum('remaining_value'),
                    'expires_at' => $first->expires_at,
                ];
            })
            ->filter(function ($ticket) {
                // Additional filter to remove tickets with 0 remaining_value after aggregation
                return $ticket['remaining_value'] === null || $ticket['remaining_value'] > 0;
            });
        return view('profile', [
            'user' => $user,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $path = $file->store('profile_pictures', 'public');
            // Delete old picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $path;
        } else {
            unset($data['profile_picture']); // Don't overwrite if not uploading
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
