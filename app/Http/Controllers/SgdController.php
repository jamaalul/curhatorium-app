<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SgdGroup;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SgdController extends Controller
{
    public function show(Request $request) {
        $query = SgdGroup::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('topic', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category') && $request->get('category') !== 'all') {
            $query->where('category', $request->get('category'));
        }
        
        // Filter by status (upcoming/past)
        if ($request->filled('status')) {
            $now = now();
            if ($request->get('status') === 'upcoming') {
                $query->where('schedule', '>', $now);
            } elseif ($request->get('status') === 'past') {
                $query->where('schedule', '<', $now);
            }
        }
        
        // Sort functionality
        $sortBy = $request->get('sort', 'schedule');
        $sortOrder = $request->get('order', 'asc');
        
        if (in_array($sortBy, ['title', 'category', 'schedule', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $groups = $query->get();
        
        // Get unique categories for filter dropdown
        $categories = SgdGroup::distinct()->pluck('category')->sort()->values();
        
        return view('sgd.sgd', compact('groups', 'categories'));
    }

    public function getGroups() {
        $Group = SgdGroup::all();
        return response()->json($Group);
    }

    public function joinGroup(Request $request) {
        $request->validate([
            'group_id' => ['required', 'exists:sgd_groups,id'],
        ]);

        $user = Auth::user();
        if (!$user instanceof \App\Models\User) {
            return redirect()->route('sgd')->with('error', 'User not found or invalid.');
        }

        $groupId = $request->get('group_id');
        
        // Check if user has already joined this group
        if ($user->hasJoinedSgdGroup($groupId)) {
            return redirect()->route('sgd')->with('error', 'You have already joined this group.');
        }

        // Get the group
        $group = SgdGroup::findOrFail($groupId);
        
        // Check if the group is in the past
        if ($group->hasStarted()) {
            return redirect()->route('sgd')->with('error', 'This group has already started.');
        }

        // Join the group
        $user->sgdGroups()->attach($groupId);
        
        return redirect()->route('sgd')->with('success', 'Successfully joined the group. You will get notification when the SGD is about to start');
    }

    public function enterMeetingRoom(Request $request) {
        $request->validate([
            'group_id' => ['required', 'exists:sgd_groups,id'],
        ]);

        $user = Auth::user();
        if (!$user instanceof \App\Models\User) {
            return redirect()->route('sgd')->with('error', 'User not found or invalid.');
        }

        $groupId = $request->get('group_id');
        $group = SgdGroup::findOrFail($groupId);
        
        // Check if user has joined this group
        if (!$user->hasJoinedSgdGroup($groupId)) {
            return redirect()->route('sgd')->with('error', 'You must join the group first before entering the meeting room.');
        }

        // Check if the group has already ended (more than 2 hours after start)
        $endTime = $group->schedule->addHours(2);
        if (now()->gt($endTime)) {
            return redirect()->route('sgd')->with('error', 'This group session has ended.');
        }

        // Redirect to meeting room (you can implement the actual meeting room logic here)
        return redirect()->away($group->meeting_address)->with('success', 'Entering meeting room...');
    }

    public function leaveGroup(Request $request) {
        $request->validate([
            'group_id' => ['required', 'exists:sgd_groups,id'],
        ]);

        $user = Auth::user();
        if (!$user instanceof \App\Models\User) {
            return redirect()->route('sgd')->with('error', 'User not found or invalid.');
        }

        $groupId = $request->get('group_id');
        $group = SgdGroup::findOrFail($groupId);
        
        // Check if user has joined this group
        if (!$user->hasJoinedSgdGroup($groupId)) {
            return redirect()->route('sgd')->with('error', 'You are not a member of this group.');
        }

        // Check if the group has already started
        if ($group->hasStarted()) {
            return redirect()->route('sgd')->with('error', 'Cannot leave a group that has already started.');
        }

        // Leave the group
        $user->sgdGroups()->detach($groupId);
        
        return redirect()->route('sgd')->with('success', 'Successfully left the group.');
    }
}
