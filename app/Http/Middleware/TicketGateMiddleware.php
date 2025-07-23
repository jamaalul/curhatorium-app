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

        // Fetch all valid tickets for this feature
        $tickets = $user->userTickets()
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', Carbon::now())
            ->whereIn('limit_type', ['count', 'day'])
            ->where('remaining_value', '>', 0)
            ->orderBy('expires_at')
            ->get();

        // For unlimited/hour, fallback to old logic
        $ticket = $user->userTickets()
            ->where('ticket_type', $ticketType)
            ->where('expires_at', '>', Carbon::now())
            ->orderByDesc('remaining_value')
            ->first();

        if (!$ticket) {
            return redirect()->back()->with('error', 'Anda tidak memiliki tiket yang valid untuk fitur ini.');
        }

        // Unlimited: always allow
        if ($ticket->limit_type === 'unlimited') {
            return $next($request);
        }

        // Hour-based: show modal or consume
        if ($ticket->limit_type === 'hour') {
            if ($ticket->remaining_value === null || $ticket->remaining_value <= 0) {
                return redirect()->back()->with('error', 'Tiket Anda sudah habis untuk fitur ini.');
            }
            if ($request->isMethod('post') && $request->has('consume_amount')) {
                $consume = floatval($request->input('consume_amount'));
                if ($consume <= 0 || $consume > $ticket->remaining_value) {
                    return redirect()->back()->with('error', 'Jumlah waktu tidak valid atau melebihi sisa waktu.');
                }
                $ticket->remaining_value -= $consume;
                $ticket->save();
                if ($ticket->remaining_value <= 0) {
                    return redirect()->back()->with('error', 'Tiket Anda sudah habis untuk fitur ini.');
                }
                return $next($request);
            } else {
                // Show modal for hour-based
                return response()->view('components.ticket-gate-modal', [
                    'ticket' => $ticket,
                    'ticketType' => $ticketType,
                ]);
            }
        }

        // Count/day: sum all valid tickets
        if (in_array($ticket->limit_type, ['count', 'day'])) {
            $total_remaining = $tickets->sum('remaining_value');
            if ($total_remaining <= 0) {
                return redirect()->back()->with('error', 'Tiket Anda sudah habis untuk fitur ini.');
            }
            // Accept GET with redeem=1 or POST for consumption
            if (($request->isMethod('get') && $request->input('redeem') == '1') || $request->isMethod('post')) {
                // Decrement from the ticket with the earliest expiration
                foreach ($tickets as $t) {
                    if ($t->remaining_value > 0) {
                        $t->remaining_value -= 1;
                        $t->save();
                        break;
                    }
                }
                // Recalculate after decrement
                $total_remaining = $tickets->sum('remaining_value');
                if ($total_remaining < 0) {
                    return redirect()->back()->with('error', 'Tiket Anda sudah habis untuk fitur ini.');
                }
                return $next($request);
            } else {
                // Show modal for count/day, pass total_remaining
                return response()->view('components.ticket-gate-modal', [
                    'ticket' => $ticket, // for info
                    'ticketType' => $ticketType,
                    'total_remaining' => $total_remaining,
                ]);
            }
        }

        // Unknown type
        return redirect()->back()->with('error', 'Tipe tiket tidak dikenali.');
    }
} 