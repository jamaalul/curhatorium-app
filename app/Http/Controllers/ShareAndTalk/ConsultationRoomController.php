<?php

namespace App\Http\Controllers\ShareAndTalk;

use App\Events\StatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConsultationRoomController extends Controller
{
    public function chatRoom($room)
    {
        Log::info("Chat room access attempt for room: {$room}", [
            'is_professional' => Auth::guard('professional')->check(),
            'professional_id' => Auth::guard('professional')->id(),
            'is_user' => Auth::check(),
            'user_id' => Auth::id(),
        ]);

        $consultation = Consultation::where('room', $room)->first();

        if (! $consultation) {
            Log::warning('Room not found in chatRoom: '.$room);

            return redirect()->route('share-and-talk.index')->with('error', 'Ruang obrolan tidak ditemukan.');
        }

        // Update status to online for the current user
        $statusType = Auth::guard('professional')->check() ? 'facilitator' : 'client';
        $columnName = $statusType.'_status';
        $consultation->update([$columnName => 'online']);
        $consultation->refresh();

        // Broadcast the status update
        StatusUpdated::dispatch($room, $statusType, 'online', $consultation);

        $messages = ConsultationMessage::where('consultation_id', $consultation->id)->orderBy('created_at', 'asc')->get();

        return view('share-and-talk.chat', [
            'room' => $room,
            'messages' => $messages,
            'consultation' => $consultation,
        ]);
    }

    public function videoRoom($room)
    {
        Log::info("Video room access attempt for room: {$room}", [
            'is_professional' => Auth::guard('professional')->check(),
            'professional_id' => Auth::guard('professional')->id(),
            'is_user' => Auth::check(),
            'user_id' => Auth::id(),
        ]);

        $consultation = Consultation::where('room', $room)->first();
        if (! $consultation) {
            return redirect()->route('share-and-talk.index')->with('error', 'Ruang obrolan tidak ditemukan.');
        }

        return view('share-and-talk.video', ['room' => $room, 'consultation' => $consultation]);
    }
}
