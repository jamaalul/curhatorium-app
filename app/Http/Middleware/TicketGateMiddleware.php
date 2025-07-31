<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TicketGateMiddleware
{
    public function handle(Request $request, Closure $next, $ticketType)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $allTickets = $user->userTickets()
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', Carbon::now())
            ->get();

        if ($allTickets->isEmpty()) {
            return redirect()->back()->withErrors(['msg' => 'Anda tidak memiliki tiket yang valid untuk fitur ini.']);
        }

        // If any ticket is unlimited, always allow
        if ($allTickets->contains(function ($t) { return $t->limit_type === 'unlimited'; })) {
            return $next($request);
        }
        // If any day-based ticket has remaining_value=null, treat as unlimited
        if ($allTickets->contains(function ($t) { return $t->limit_type === 'day' && is_null($t->remaining_value); })) {
            return $next($request);
        }

        // Hour-based: only consider hour tickets
        $hourTicket = $user->userTickets()
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', Carbon::now())
            ->where('limit_type', 'hour')
            ->where('remaining_value', '>', 0)
            ->orderByDesc('remaining_value')
            ->first();
        // Allow access if user has a valid timer in session (for mentai_chatbot)
        if ($ticketType === 'mentai_chatbot') {
            $endTime = session('chatbot_end_time');
            if ($endTime && $endTime > Carbon::now()->timestamp) {
                return $next($request);
            }
        }
        if ($hourTicket) {
            if (($request->isMethod('get') && $request->has('consume_amount')) || ($request->isMethod('post') && $request->has('consume_amount'))) {
                // Convert minutes to decimal hours
                $minutes = intval($request->input('consume_amount'));
                $consume = $minutes / 60.0;
                if ($consume <= 0 || $consume > $hourTicket->remaining_value) {
                    return redirect()->back()->withErrors(['msg' => 'Jumlah waktu tidak valid atau melebihi sisa waktu.']);
                }
                $hourTicket->remaining_value -= $consume;
                $hourTicket->save();
                
                // Clean up any tickets that should be deleted
                \App\Models\UserTicket::cleanupAfterConsumption();
                
                if ($hourTicket->remaining_value <= 0) {
                    return redirect()->back()->withErrors(['msg' => 'Tiket Anda sudah habis untuk fitur ini.']);
                }
                return $next($request);
            } else {
                // Show modal for hour-based
                return response()->view('components.ticket-gate-modal', [
                    'ticket' => $hourTicket,
                    'ticketType' => $ticketType,
                ]);
            }
        }

        // Count-based: only consider count tickets
        $countTickets = $user->userTickets()
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', Carbon::now())
            ->where('limit_type', 'count')
            ->where('remaining_value', '>', 0)
            ->orderBy('expires_at')
            ->get();
        if ($countTickets->isNotEmpty()) {
            $total_remaining = $countTickets->sum('remaining_value');
            if ($total_remaining <= 0) {
                return redirect()->back()->withErrors(['msg' => 'Tiket Anda sudah habis untuk fitur ini.']);
            }
            if (($request->isMethod('get') && $request->input('redeem') == '1') || $request->isMethod('post')) {
                $consumedTicket = null;
                foreach ($countTickets as $t) {
                    if ($t->remaining_value > 0) {
                        $t->remaining_value -= 1;
                        $t->save();
                        $consumedTicket = $t;
                        break;
                    }
                }
                
                // Track SGD ticket consumption for payment purposes
                if ($ticketType === 'support_group' && $consumedTicket) {
                    $this->trackSgdTicketConsumption($user, $consumedTicket);
                }
                
                // Track Share and Talk ticket consumption for payment purposes
                if ($ticketType === 'share_talk_ranger_chat' && $consumedTicket) {
                    $this->trackShareTalkTicketConsumption($user, $consumedTicket);
                }
                
                // Clean up any tickets that should be deleted
                \App\Models\UserTicket::cleanupAfterConsumption();
                $total_remaining = $countTickets->sum('remaining_value');
                if ($total_remaining < 0) {
                    return redirect()->back()->withErrors(['msg' => 'Tiket Anda sudah habis untuk fitur ini.']);
                }
                return $next($request);
            } else {
                return response()->view('components.ticket-gate-modal', [
                    'ticket' => $countTickets->first(),
                    'ticketType' => $ticketType,
                    'total_remaining' => $total_remaining,
                ]);
            }
        }

        // Day-based: only consider day tickets
        $dayTickets = $user->userTickets()
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', Carbon::now())
            ->where('limit_type', 'day')
            ->where('remaining_value', '>', 0)
            ->orderBy('expires_at')
            ->get();
        if ($dayTickets->isNotEmpty()) {
            $total_remaining = $dayTickets->sum('remaining_value');
            if ($total_remaining <= 0) {
                return redirect()->back()->withErrors(['msg' => 'Tiket Anda sudah habis untuk fitur ini.']);
            }
            $sessionKey = 'ticket_day_'.$ticketType.'_'.($user->id);
            $today = Carbon::now()->toDateString();
            $lastAccess = session($sessionKey);
            if ($lastAccess === $today) {
                // Already accessed today, do not decrement
                return $next($request);
            }
            // Not accessed today, show modal
            if (($request->isMethod('get') && $request->input('redeem') == '1') || $request->isMethod('post')) {
                foreach ($dayTickets as $t) {
                    if ($t->remaining_value > 0) {
                        $t->remaining_value -= 1;
                        $t->save();
                        break;
                    }
                }
                
                // Clean up any tickets that should be deleted
                \App\Models\UserTicket::cleanupAfterConsumption();
                
                session([$sessionKey => $today]);
                $total_remaining = $dayTickets->sum('remaining_value');
                if ($total_remaining < 0) {
                    return redirect()->back()->withErrors(['msg' => 'Tiket Anda sudah habis untuk fitur ini.']);
                }
                return $next($request);
            } else {
                return response()->view('components.ticket-gate-modal', [
                    'ticket' => $dayTickets->first(),
                    'ticketType' => $ticketType,
                    'total_remaining' => $total_remaining,
                ]);
            }
        }

        // Unknown type
        return redirect()->back()->withErrors(['msg' => 'Tipe tiket tidak dikenali.']);
    }

    private function trackSgdTicketConsumption($user, $consumedTicket)
    {
        // Determine if this is the user's first SGD while having active Calm Starter membership
        $isFirstSgdWithCalmStarter = $this->isFirstSgdWithCalmStarter($user);
        
        // Determine ticket source
        $ticketSource = $isFirstSgdWithCalmStarter ? 'calm_starter' : 'paid';
        
        // Get SGD group ID from request (assuming it's passed as group_id)
        $sgdGroupId = request('group_id');
        
        if ($sgdGroupId) {
            \App\Models\SgdTicketConsumption::create([
                'user_id' => $user->id,
                'sgd_group_id' => $sgdGroupId,
                'ticket_source' => $ticketSource,
                'consumed_at' => Carbon::now(),
            ]);
        }
    }
    
    private function isFirstSgdWithCalmStarter($user)
    {
        $now = Carbon::now();
        
        // Find the user's current Calm Starter cycle
        // Get the most recent Calm Starter membership for this user
        $currentCalmStarter = $user->userMemberships()
            ->whereHas('membership', function($query) {
                $query->where('name', 'Calm Starter');
            })
            ->where('started_at', '<=', $now)
            ->where('expires_at', '>=', $now)
            ->orderBy('started_at', 'desc')
            ->first();
            
        if (!$currentCalmStarter) {
            return false; // No active Calm Starter, so it's a paid ticket
        }
        
        // Check if this is the user's first SGD participation in this Calm Starter cycle
        $hasPreviousSgdThisCycle = $user->sgdGroups()
            ->wherePivot('joined_at', '>=', $currentCalmStarter->started_at)
            ->wherePivot('joined_at', '<=', $currentCalmStarter->expires_at)
            ->exists();
        
        return !$hasPreviousSgdThisCycle; // First SGD in this Calm Starter cycle = true, subsequent = false
    }

    private function trackShareTalkTicketConsumption($user, $consumedTicket)
    {
        // Determine if this is the user's first Share and Talk while having active Calm Starter membership
        $isFirstShareTalkWithCalmStarter = $this->isFirstShareTalkWithCalmStarter($user);
        
        // Determine ticket source
        $ticketSource = $isFirstShareTalkWithCalmStarter ? 'calm_starter' : 'paid';
        
        // Get chat session ID from request (assuming it's passed as session_id)
        $chatSessionId = request('session_id');
        
        if ($chatSessionId) {
            \App\Models\ShareTalkTicketConsumption::create([
                'user_id' => $user->id,
                'chat_session_id' => $chatSessionId,
                'ticket_source' => $ticketSource,
                'consumed_at' => Carbon::now(),
            ]);
        }
    }
    
    private function isFirstShareTalkWithCalmStarter($user)
    {
        $now = Carbon::now();
        
        // Find the user's current Calm Starter cycle
        // Get the most recent Calm Starter membership for this user
        $currentCalmStarter = $user->userMemberships()
            ->whereHas('membership', function($query) {
                $query->where('name', 'Calm Starter');
            })
            ->where('started_at', '<=', $now)
            ->where('expires_at', '>=', $now)
            ->orderBy('started_at', 'desc')
            ->first();
            
        if (!$currentCalmStarter) {
            return false; // No active Calm Starter, so it's a paid ticket
        }
        
        // Check if this is the user's first Share and Talk participation in this Calm Starter cycle
        $hasPreviousShareTalkThisCycle = \App\Models\ShareTalkTicketConsumption::where('user_id', $user->id)
            ->where('ticket_source', 'calm_starter')
            ->where('consumed_at', '>=', $currentCalmStarter->started_at)
            ->where('consumed_at', '<=', $currentCalmStarter->expires_at)
            ->exists();
        
        return !$hasPreviousShareTalkThisCycle; // First Share and Talk in this Calm Starter cycle = true, subsequent = false
    }
} 