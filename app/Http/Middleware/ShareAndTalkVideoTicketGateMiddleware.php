<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Professional;
use Illuminate\Support\Facades\App;

class ShareAndTalkVideoTicketGateMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $professionalId = $request->route('professionalId');
        $professional = Professional::find($professionalId);
        if (!$professional) {
            return redirect()->back()->withErrors(['msg' => 'Professional not found.']);
        }
        
        $type = $professional->type;
        
        // For video consultations, use video ticket types
        if ($type === 'psychiatrist') {
            $ticketType = 'share_talk_psy_video';
        } else {
            // Rangers don't have video consultations, so redirect with error
            return redirect()->back()->withErrors(['msg' => 'Video consultations are only available with psychiatrists.']);
        }
        
        // Forward to TicketGateMiddleware with the correct ticket type
        return App::make(TicketGateMiddleware::class)
            ->handle($request, $next, $ticketType);
    }
} 