<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SgdGroup;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\SgdPaymentService;
use Carbon\Carbon;

class SgdController extends Controller
{
    protected $paymentService;

    public function __construct(SgdPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

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
        $sortOrder = $request->get('order', 'desc'); // Default to desc for newest first
        
        if (in_array($sortBy, ['title', 'category', 'schedule', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $groups = $query->with('host')->get();
        
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
        
        // Award XP for joining support group discussion
        $xpResult = $user->awardXp('support_group');
        
        $message = 'Successfully joined the group. You will get notification when the SGD is about to start';
        if ($xpResult['success'] && $xpResult['xp_awarded'] > 0) {
            $message .= " +{$xpResult['xp_awarded']} XP earned!";
        }
        
        return redirect()->route('sgd')->with('success', $message);
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

        // If the meeting has started and is_done is not set, mark as done
        if ($group->hasStarted() && !$group->is_done) {
            $group->is_done = true;
            $group->save();
        }

        // Redirect directly to the external meeting address (e.g., YouTube URL)
        return redirect()->away($group->meeting_address);
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

    /**
     * Get payment data for a specific SGD group (admin only)
     */
    public function getPaymentData($groupId)
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $paymentData = $this->paymentService->calculateHostPayment($groupId);
        return response()->json($paymentData);
    }

    /**
     * Get consumption details for a specific SGD group (admin only)
     */
    public function getConsumptionDetails($groupId)
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $details = $this->paymentService->getGroupConsumptionDetails($groupId);
        return response()->json($details);
    }

    /**
     * Get payment summary for a date range (admin only)
     */
    public function getPaymentSummary(Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $summary = $this->paymentService->getPaymentSummary($startDate, $endDate);
        return response()->json($summary);
    }

    // public function groupMeet(Request $request, $address) {
    //     $user = Auth::user();
    //     if (!$user instanceof \App\Models\User) {
    //         return redirect()->route('sgd')->with('error', 'User not found or invalid.');
    //     }

    //     return view('sgd.meeting', compact('address'));
        
    // }
}
