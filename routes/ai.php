<?php

use App\Ai\Agents\MentAI;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Ai\Models\Conversation;

Route::post('/ai/start', function (Request $request) {
    $conversation = $request->user()->conversations()->create([
        'id' => (string) Str::uuid(),
        'title' => 'Percakapan baru', // "New conversation" — required field, no default
    ]);

    return response()->json([
        'conversationId' => $conversation->id,
    ]);
})->middleware('auth');

// Send a message in an AI conversation (used for every message, including the first)
Route::post('/ai/{conversation}/message', function (Request $request, Conversation $conversation) {
    $request->validate(['message' => 'required|string']);

    return (new MentAI)
        ->continue($conversation->id, as: $request->user())
        ->stream($request->input('message'));
})->middleware('auth');

// List a user's AI conversations
Route::get('/ai/conversations', function (Request $request) {
    return $request->user()->conversations()->latest('updated_at')->paginate(20);
})->middleware('auth');

// Fetch messages for a specific conversation
Route::get('/ai/{conversation}/messages', function (Request $request, Conversation $conversation) {
    if ($conversation->user_id !== $request->user()->id) {
        abort(403);
    }
    
    return $conversation->messages()->oldest()->get();
})->middleware('auth');
