<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membership;
use App\Models\MembershipTicket;
use App\Models\UserMembership;
use App\Models\UserTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::where('is_active', true)->get();
        return view('membership.index', compact('memberships'));
    }

    public function buy($id)
    {
        $user = Auth::user();
        $membership = Membership::findOrFail($id);
        $now = Carbon::now();
        $expires = $membership->duration_days > 0 ? $now->copy()->addDays($membership->duration_days) : null;

        // Memberships that can be stacked
        $stackable = [
            'Calm Starter',
            'Harmony',
            'Serenity',
            "Chat with Sanny's Aid",
            "Meet with Sanny's Aid",
        ];

        // Prevent multiple active subscriptions except for stackable ones
        if (!in_array($membership->name, $stackable)) {
            $hasActive = UserMembership::where('user_id', $user->id)
                ->whereHas('membership', function($q) use ($stackable) {
                    $q->whereNotIn('name', $stackable);
                })
                ->where('expires_at', '>', $now)
                ->exists();
            if ($hasActive) {
                return redirect()->route('membership.index')->with('error', 'Anda sudah memiliki langganan aktif. Tidak dapat membeli lebih dari satu langganan utama pada saat yang sama.');
            }
        }

        // Create user_membership
        $userMembership = UserMembership::create([
            'user_id' => $user->id,
            'membership_id' => $membership->id,
            'started_at' => $now,
            'expires_at' => $expires ?? $now->copy()->addMonth(), // fallback 1 month
        ]);

        // Grant tickets
        $tickets = MembershipTicket::where('membership_id', $membership->id)->get();
        foreach ($tickets as $ticket) {
            // Treat as unlimited if limit_type is 'unlimited' OR limit_value is null
            $isUnlimited = $ticket->limit_type === 'unlimited' || is_null($ticket->limit_value);
            $value = $isUnlimited ? null : $ticket->limit_value;
            UserTicket::create([
                'user_id' => $user->id,
                'ticket_type' => $ticket->ticket_type,
                'limit_type' => $isUnlimited ? 'unlimited' : $ticket->limit_type,
                'limit_value' => $isUnlimited ? null : $value,
                'remaining_value' => $isUnlimited ? null : $value,
                'expires_at' => $expires ?? $now->copy()->addMonth(),
            ]);
        }

        // For now, just return a simple confirmation view/alert
        return redirect()->route('membership.index')->with('success', 'Membership purchased! Tickets granted.');
    }
}
