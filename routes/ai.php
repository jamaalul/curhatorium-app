<?php

use App\Http\Controllers\AiConversationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/mental-support-chatbot', [AiConversationController::class, 'create'])->name('mentai.index');
    Route::get('/mental-support-chatbot/{conversation}', [AiConversationController::class, 'show'])->name('mentai.show');

    Route::post('/ai/start', [AiConversationController::class, 'start']);
    Route::post('/ai/{conversation}/message', [AiConversationController::class, 'sendMessage']);
    Route::post('/ai/{conversation}/generate-title', [AiConversationController::class, 'generateTitle']);
    Route::get('/ai/conversations', [AiConversationController::class, 'index']);
    Route::get('/ai/{conversation}/messages', [AiConversationController::class, 'showMessages']);
});
