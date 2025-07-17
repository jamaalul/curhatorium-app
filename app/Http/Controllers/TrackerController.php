<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrackerController extends Controller
{
    public function index() {
        return view("tracker.index");
    }

    public function track(Request $request) {
        // Log the incoming request data for debugging
        Log::info('Tracker form submission', $request->all());
        
        // Validate the request
        $validated = $request->validate([
            'mood' => 'required|integer|min:1|max:10',
            'activity' => 'required|string|in:work,exercise,social,hobbies,rest,entertainment,nature,food,health,other',
            'activityExplanation' => 'nullable|string|max:500',
            'energy' => 'required|integer|min:1|max:10',
            'productivity' => 'required|integer|min:1|max:10',
        ]);
            // Create the stat record
        $stat = Stat::create([
            'user_id' => Auth::user()->id,
            'mood' => $validated['mood'],
            'activity' => $validated['activity'],
            'explanation' => $validated['activityExplanation'],
            'energy' => $validated['energy'],
            'productivity' => $validated['productivity'],
            'day' => now()->format('l'), // e.g., 'Monday', 'Tuesday', etc.
        ]);

        Log::info('Mood entry created successfully', ['stat_id' => $stat->id]);

        // Return success response
        return view('tracker.result', ['stat'=> $stat]);
    }
}
