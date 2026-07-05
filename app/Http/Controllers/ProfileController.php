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

        return view('profile', [
            'user' => $user,
            'tickets' => collect(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle profile picture upload with enhanced security
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            // Additional security checks
            $fileName = $file->getClientOriginalName();
            $fileExtension = strtolower($file->getClientOriginalExtension());

            // Validate file extension
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (! in_array($fileExtension, $allowedExtensions)) {
                return Redirect::back()->withErrors(['profile_picture' => 'File type not allowed.']);
            }

            // Validate file name (prevent path traversal)
            if (preg_match('/[\/\\\\]/', $fileName) || strpos($fileName, '..') !== false) {
                return Redirect::back()->withErrors(['profile_picture' => 'Invalid file name.']);
            }

            // Generate secure filename
            $secureFileName = 'profile_'.$user->id.'_'.time().'_'.uniqid().'.'.$fileExtension;

            // Store file with secure name
            $path = $file->storeAs('profile_pictures', $secureFileName, 'public');

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
