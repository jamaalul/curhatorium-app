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
        $message = $request->input('message');
        $room = $request->input('room');
        $userId = $request->user()->id;

        MessageV2::create([
            'user_id' => $userId,
            'room' => $room,
            'message' => $message,
        ]);

        MessageSent::dispatch($message, $room, $userId);
        return response()->json(['status' => 'Message sent']);
    }

    public function terminate(Request $request, $room)
    {
        MessageSent::dispatch('Session terminated', $room, 0);
        return redirect()->route('pusher.index');
    }
}
