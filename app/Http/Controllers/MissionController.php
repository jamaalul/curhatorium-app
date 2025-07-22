<?php
namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\MissionCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Use the current date as a seed for consistent daily "randomness"
        $dailySeed = (int) date('Ymd');

        $easyMissions = Mission::where('difficulty', 'easy')->get()->shuffle($dailySeed)->take(5);
        $mediumMissions = Mission::where('difficulty', 'medium')->get()->shuffle($dailySeed)->take(5);
        $hardMissions = Mission::where('difficulty', 'hard')->get()->shuffle($dailySeed)->take(5);

        $missions = [
            'easy' => $easyMissions,
            'medium' => $mediumMissions,
            'hard' => $hardMissions,
        ];

        $missionIds = collect($missions)->flatten()->pluck('id');
        $completions = MissionCompletion::where('user_id', $user->id)
            ->whereIn('mission_id', $missionIds)
            ->pluck('mission_id')
            ->toArray();
            
        return view('missions', [
            'missions' => $missions,
            'completedMissions' => $completions,
        ]);
    }

    public function complete(Request $request, $missionId)
    {
        $request->validate([
            'reflection' => 'required|string',
            'feeling' => 'required|string',
        ]);
        $user = Auth::user();
        $mission = Mission::findOrFail($missionId);
        $alreadyCompleted = MissionCompletion::where('user_id', $user->id)
            ->where('mission_id', $missionId)
            ->exists();
        if ($alreadyCompleted) {
            return back()->with('error', 'You have already completed this mission.');
        }
        MissionCompletion::create([
            'user_id' => $user->id,
            'mission_id' => $missionId,
            'reflection' => $request->reflection,
            'feeling' => $request->feeling,
            'completed_at' => now(),
        ]);
        return back()->with('success', 'Mission completed!');
    }
} 