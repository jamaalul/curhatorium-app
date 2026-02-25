<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\StatusUpdated;
use App\Models\Consultation;
use App\Models\MessageV2;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PusherController extends Controller
{
    public function index()
    {
        return view('pusher.index');
    }

    public function createRoom(Request $request)
    {
        $room = Str::random(10);
        return redirect()->route('pusher.room', ['room' => $room]);
    }

    public function room($room)
    {
        return view('pusher.room', ['room' => $room]);
    }

    public function sendMessage(Request $request)
    {
        $messageContent = $request->input('message');
        $room = $request->input('room');
        
        $sender = auth()->guard('web')->user() ?? auth()->guard('professional')->user();

        if (!$sender) {
            return response()->json(['status' => 'Unauthenticated'], 401);
        }

        $message = MessageV2::create([
            'room' => $room,
            'message' => $messageContent,
            'sender_id' => $sender->id,
            'sender_type' => get_class($sender),
        ]);

        // Eager load the sender relationship
        $message->load('sender');

        MessageSent::dispatch($message);
        return response()->json(['status' => 'Message sent']);
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

        // Find the consultation by room
        $consultation = Consultation::where('room', $room)->first();

        if (!$consultation) {
            return response()->json(['status' => 'Room not found'], 404);
        }

        // Update the status
        $columnName = $statusType . '_status';
        $consultation->update([$columnName => $status]);

        // Reload the consultation with updated data
        $consultation->refresh();

        // Broadcast the status update
        StatusUpdated::dispatch($room, $statusType, $status, $consultation);

        return response()->json(['status' => 'Status updated successfully']);
    }
}

