<?php

namespace App\Http\Controllers;

use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class XpController extends Controller
{
    protected $xpService;

    public function __construct(XpService $xpService)
    {
        $this->xpService = $xpService;
    }

    /**
     * Award XP to the authenticated user
     */
    public function awardXp(Request $request): JsonResponse
    {
        $request->validate([
            'activity' => 'required|string',
            'quantity' => 'integer|min:1'
        ]);

        $user = auth()->user();
        $result = $user->awardXp($request->activity, $request->quantity ?? 1);

        return response()->json($result);
    }

    /**
     * Get user's XP progress
     */
    public function getXpProgress(): JsonResponse
    {
        $user = auth()->user();
        $progress = $user->getXpProgress();

        return response()->json($progress);
    }

    /**
     * Get daily XP summary
     */
    public function getDailyXpSummary(): JsonResponse
    {
        $user = auth()->user();
        $summary = $user->getDailyXpSummary();

        return response()->json($summary);
    }

    /**
     * Get XP breakdown for all activities
     */
    public function getXpBreakdown(): JsonResponse
    {
        $user = auth()->user();
        $breakdown = $user->getXpBreakdown();

        return response()->json($breakdown);
    }

    /**
     * Check if user can access psychologist
     */
    public function canAccessPsychologist(): JsonResponse
    {
        $user = auth()->user();
        $canAccess = $user->canAccessPsychologist();

        return response()->json([
            'can_access' => $canAccess,
            'total_xp' => $user->total_xp,
            'required_xp' => XpService::TOTAL_XP_FOR_PSYCHOLOGIST
        ]);
    }

    /**
     * Get user's XP history
     */
    public function getXpHistory(): JsonResponse
    {
        $user = auth()->user();
        $history = $user->dailyXpLogs()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($history);
    }
} 