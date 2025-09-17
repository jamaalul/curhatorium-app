<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ShareAndTalkService;
use App\Services\TicketService;
use App\Http\Requests\ChatMessageRequest;
use App\Models\Professional;
use App\Models\ChatSession;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShareAndTalkController extends Controller
{
    public function __construct(
        private ShareAndTalkService $shareAndTalkService,
        private TicketService $ticketService
    ) {}
    public function index() {
        return view('share-and-talk.index');
    }

    public function getProfessionals(Request $request)
    {
        $type = $request->query('type');
        $professionals = $this->shareAndTalkService->getProfessionals($type);
        
        return response()->json($professionals);
    }

    public function wait() {
        return view('share-and-talk.waiting');
    }
}