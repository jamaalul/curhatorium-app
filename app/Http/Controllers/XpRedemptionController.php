<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class XpRedemptionController extends Controller
{
    // XP Redemption Scheme based on the image
    const REDEMPTION_SCHEME = [
        'share_talk_psy_video' => [
            'name' => 'Share and Talk psy video call',
            'xp_cost' => 10000,
            'ticket_type' => 'share_talk_psy_video',
            'limit_type' => 'count',
            'limit_value' => 1,
            'remaining_value' => 1,
            'duration_days' => 30,
            'is_unlimited' => false,
        ],
        'share_talk_psy_chat' => [
            'name' => 'Share and Talk psy chat',
            'xp_cost' => 7000,
            'ticket_type' => 'share_talk_psy_chat',
            'limit_type' => 'count',
            'limit_value' => 1,
            'remaining_value' => 1,
            'duration_days' => 30,
            'is_unlimited' => false,
        ],
        'share_talk_partner_chat' => [
            'name' => 'Share and talk partner chat',
            'xp_cost' => 4400,
            'ticket_type' => 'share_talk_ranger_chat',
            'limit_type' => 'count',
            'limit_value' => 1,
            'remaining_value' => 1,
            'duration_days' => 30,
            'is_unlimited' => false,
        ],
        'support_group_discussion' => [
            'name' => 'Support group discussion',
            'xp_cost' => 4000,
            'ticket_type' => 'support_group',
            'limit_type' => 'count',
            'limit_value' => 1,
            'remaining_value' => 1,
            'duration_days' => 30,
            'is_unlimited' => false,
        ],
        'mood_tracker' => [
            'name' => 'Mood & Productivity Tracker',
            'xp_cost' => 4000,
            'ticket_type' => 'tracker',
            'limit_type' => 'day',
            'limit_value' => null,
            'remaining_value' => null,
            'duration_days' => 30,
            'is_unlimited' => true,
        ],
    ];

    public function index()
    {
        $user = Auth::user();
        $redemptionScheme = self::REDEMPTION_SCHEME;

        return view('xp-redemption.index', compact('user', 'redemptionScheme'));
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'ticket_type' => 'required|string|in:'.implode(',', array_keys(self::REDEMPTION_SCHEME)),
        ]);

        $user = Auth::user();
        $ticketType = $request->ticket_type;
        $scheme = self::REDEMPTION_SCHEME[$ticketType];

        // Check if user has enough XP
        if ($user->total_xp < $scheme['xp_cost']) {
            return back()->with('error', 'Insufficient XP. You need '.$scheme['xp_cost'].' XP to redeem this ticket.');
        }

        try {
            // Deduct XP from user
            $user->total_xp -= $scheme['xp_cost'];
            $user->save();

            // Create single user ticket
            UserTicket::create([
                    'user_id' => $user->id,
                    'ticket_type' => $scheme['ticket_type'],
                    'limit_type' => $scheme['limit_type'],
                    'limit_value' => $scheme['limit_value'],
                    'remaining_value' => $scheme['remaining_value'],
                    'expires_at' => Carbon::now()->addDays($scheme['duration_days']),
                ]);

            return back()->with('success', 'Successfully redeemed '.$scheme['name'].' for '.$scheme['xp_cost'].' XP!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to redeem ticket. Please try again.');
        }
    }
}
