<?php

namespace App\Http\Controllers;

use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class XpController extends Controller
{
    public function __construct(
        private XpService $xpService
    ) {}

    /**
     * Award XP to the authenticated user
     */
    public function awardXp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'activity' => 'required|string',
            'quantity' => 'integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $result = $this->xpService->awardXp(
            $user, 
            $request->activity, 
            $request->quantity ?? 1
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get XP progress for the authenticated user
     */
    public function getXpProgress(): JsonResponse
    {
        $user = Auth::user();
        $progress = $this->xpService->getXpProgress($user);

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    /**
     * Get daily XP summary for the authenticated user
     */
    public function getDailyXpSummary(): JsonResponse
    {
        $user = Auth::user();
        $summary = $this->xpService->getDailyXpSummary($user);

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get XP breakdown for the authenticated user
     */
    public function getXpBreakdown(): JsonResponse
    {
        $user = Auth::user();
        $breakdown = $this->xpService->getXpBreakdown($user);

        return response()->json([
            'success' => true,
            'data' => $breakdown
        ]);
    }

    /**
     * Check if user can access psychologist
     */
    public function canAccessPsychologist(): JsonResponse
    {
        $user = Auth::user();
        $canAccess = $this->xpService->canAccessPsychologist($user);

        return response()->json([
            'success' => true,
            'data' => [
                'can_access' => $canAccess,
                'current_xp' => $user->total_xp,
                'required_xp' => config('xp.targets.psychologist_access')
            ]
        ]);
    }

    /**
     * Get XP history for the authenticated user
     */
    public function getXpHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days' => 'integer|min:1|max:365'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $history = $this->xpService->getXpHistory($user, $request->days ?? 30);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
} 