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
                foreach ($countTickets as $t) {
                    if ($t->remaining_value > 0) {
                        $t->remaining_value -= 1;
                        $t->save();
                        break;
                    }
                }
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
} 