<?php

namespace App\Http\Controllers\ShareAndTalk;

use App\Events\SessionMessageSent;
use App\Events\StatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ProfessionalScheduleSlot;
use App\Services\ShareAndTalkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationApiController extends Controller
{
    public function __construct(
        private ShareAndTalkService $shareAndTalkService
    ) {}

    public function endSession(Request $request)
    {
        $request->validate([
            'room' => 'required|string',
        ]);

        $room = $request->input('room');

        $consultation = Consultation::where('room', $room)->first();

        if ($consultation) {
            $consultation->update([
                'facilitator_status' => 'offline',
                'client_status' => 'offline',
                'status' => 'completed',
                'end' => now(),
            ]);

            $statusType = Auth::guard('professional')->check() ? 'facilitator' : 'client';
            StatusUpdated::dispatch($room, $statusType, 'offline', $consultation);

            if ($consultation->professional_schedule_slot_id) {
                $slot = ProfessionalScheduleSlot::find($consultation->professional_schedule_slot_id);
                if ($slot) {
                    $slot->status = 'completed';
                    $slot->save();
                }
            }
        }

        if (Auth::guard('professional')->check()) {
            return redirect()->route('professional.dashboard')->with('success', 'Sesi telah diakhiri.');
        } else {
            return redirect()->route('dashboard')->with('success', 'Sesi telah diakhiri.');
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'room' => 'required|string',
            'status_type' => 'required|string|in:facilitator,client',
            'status' => 'required|string|in:online,offline',
        ]);

        $room = $request->input('room');
        $statusType = $request->input('status_type');
        $status = $request->input('status');

        $consultation = Consultation::where('room', $room)->first();

        if (! $consultation) {
            return response()->json(['status' => 'Room not found'], 404);
        }

        $columnName = $statusType.'_status';
        $consultation->update([$columnName => $status]);
        $consultation->refresh();

        StatusUpdated::dispatch($room, $statusType, $status, $consultation);

        return response()->json(['status' => 'Status updated successfully']);
    }

    public function clientSend(Request $request)
    {
        $request->validate([
            'room' => 'required|string',
            'message' => 'required|string|max:5000',
        ]);

        $room = $request->input('room');
        $messageContent = $request->input('message');

        $user = Auth::user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $message = $this->shareAndTalkService->sendMessage($room, $messageContent, 'user', $user->id);

        SessionMessageSent::dispatch($message, $room);

        return response()->json(['status' => 'Message sent']);
    }

    public function facilitatorSend(Request $request)
    {
        $request->validate([
            'room' => 'required|string',
            'message' => 'required|string|max:5000',
        ]);

        $room = $request->input('room');
        $messageContent = $request->input('message');

        $professional = Auth::guard('professional')->user();

        if (! $professional) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $message = $this->shareAndTalkService->sendMessage($room, $messageContent, 'professional', $professional->id);

        SessionMessageSent::dispatch($message, $room);

        return response()->json(['status' => 'Message sent']);
    }

    public function getSessionStatus(string $room)
    {
        $status = $this->shareAndTalkService->getSessionStatus($room);

        if (! $status) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        return response()->json($status);
    }

    public function manualActivateSession(string $room)
    {
        $activated = $this->shareAndTalkService->activateSession($room);

        if (! $activated) {
            return response()->json(['error' => 'Session not found or cannot be activated'], 422);
        }

        return response()->json(['status' => 'Session activated']);
    }

    public function getSessionMessages(string $room)
    {
        $messages = $this->shareAndTalkService->getSessionMessages($room);

        return response()->json($messages);
    }
}
