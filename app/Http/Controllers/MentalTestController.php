<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MentalHealthTestResult;

class MentalTestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'answers' => 'required|array|size:14',
            'total_score' => 'required|integer',
            'emotional_score' => 'required|integer',
            'social_score' => 'required|integer',
            'psychological_score' => 'required|integer',
            'category' => 'required|string',
        ]);

        $result = MentalHealthTestResult::create([
            'user_id' => auth()->id(),
            'total_score' => $data['total_score'],
            'emotional_score' => $data['emotional_score'],
            'social_score' => $data['social_score'],
            'psychological_score' => $data['psychological_score'],
            'category' => $data['category'],
            'answers' => $data['answers'],
        ]);

        // Award XP for completing mental health test
        $user = auth()->user();
        $xpResult = $user->awardXp('mental_test');

        return response()->json([
            'success' => true, 
            'result_id' => $result->id,
            'xp_awarded' => $xpResult['xp_awarded'] ?? 0,
            'xp_message' => $xpResult['message'] ?? ''
        ]);
    }
} 