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
        
        // Set the random seed for consistent daily selection
        mt_srand($dailySeed);

        $easyMissions = $this->getDailyMissions('easy', 5, $dailySeed);
        $mediumMissions = $this->getDailyMissions('medium', 5, $dailySeed);
        $hardMissions = $this->getDailyMissions('hard', 5, $dailySeed);

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

    private function getDailyMissions($difficulty, $count, $seed)
    {
        // Get all missions for this difficulty
        $allMissions = Mission::where('difficulty', $difficulty)->get();
        
        if ($allMissions->count() <= $count) {
            return $allMissions;
        }
        
        // Use the seed to select missions consistently
        $selectedMissions = collect();
        $missionIds = $allMissions->pluck('id')->toArray();
        
        // Use the seed to generate consistent random indices
        mt_srand($seed + ord($difficulty[0])); // Add difficulty to seed for variety
        
        $selectedIndices = [];
        while (count($selectedIndices) < $count) {
            $randomIndex = mt_rand(0, count($missionIds) - 1);
            if (!in_array($randomIndex, $selectedIndices)) {
                $selectedIndices[] = $randomIndex;
            }
        }
        
        // Get the selected missions
        foreach ($selectedIndices as $index) {
            $selectedMissions->push($allMissions[$index]);
        }
        
        return $selectedMissions;
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

        // Award XP based on mission difficulty
        $activity = 'mission_' . $mission->difficulty;
        $xpResult = $user->awardXp($activity);

        $message = 'Mission completed!';
        if ($xpResult['success'] && $xpResult['xp_awarded'] > 0) {
            $message .= " +{$xpResult['xp_awarded']} XP earned!";
        }

        return back()->with('success', $message);
    }
} 