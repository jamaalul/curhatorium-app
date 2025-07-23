<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Professional;
use Illuminate\Support\Facades\App;

class ShareAndTalkTicketGateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $professionalId = $request->route('professionalId');
        $professional = Professional::find($professionalId);
        if (!$professional) {
            return redirect()->back()->with('error', 'Professional not found.');
        }
        $type = $professional->type;
        $ticketType = $type === 'psychiatrist' ? 'share_talk_psy_chat' : 'share_talk_ranger_chat';
        // Forward to TicketGateMiddleware with the correct ticket type
        return App::make(TicketGateMiddleware::class)
            ->handle($request, $next, $ticketType);
    }
} 