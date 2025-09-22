<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\MessageV2;
use Illuminate\Http\Request;
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

        $message = $sender->messages()->create([
            'room' => $room,
            'message' => $messageContent,
        ]);

        // Eager load the sender relationship
        $message->load('sender');

        MessageSent::dispatch($message);
        return response()->json(['status' => 'Message sent']);
    }

    public function terminate(Request $request, $room)
    {
        MessageSent::dispatch('Session terminated', $room, 0);
        return redirect()->route('pusher.index');
    }
}

