<?php

// routes/web.php
use App\Ai\Agents\MentAI;
use Illuminate\Http\Request;
use Laravel\Ai\Models\Conversation;

// Start a new AI conversation
Route::post('/ai', function (Request $request) {
    $request->validate(['message' => 'required|string']);

    return (new MentAI)
        ->forUser($request->user())
        ->stream($request->input('message'));
})->middleware('auth');

// Continue an existing AI conversation
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