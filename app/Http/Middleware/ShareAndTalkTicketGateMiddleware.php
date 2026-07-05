<?php

namespace App\Http\Middleware;

use App\Models\Professional;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ShareAndTalkTicketGateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $professionalId = $request->route('professionalId');
        $professional = Professional::find($professionalId);
        if (! $professional) {
            return redirect()->back()->withErrors(['msg' => 'Professional not found.']);
        }
        $type = $professional->type;
        $ticketType = $type === 'psychiatrist' ? 'share_talk_psy_chat' : 'share_talk_ranger_chat';

        // Forward to TicketGateMiddleware with the correct ticket type
        return App::make(TicketGateMiddleware::class)
            ->handle($request, $next, $ticketType);
    }
}
