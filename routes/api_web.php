<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ShareAndTalk\BookingController;
use App\Http\Controllers\ShareAndTalk\ConsultationApiController;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\XpController;
use Illuminate\Support\Facades\Route;

// API routes (web middleware, auth required)
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/user',           [AuthenticatedSessionController::class, 'getUser'])  ->name('user.get');
    Route::get('/agenda/pending', [AgendaController::class, 'getPending'])             ->name('getPendingAgenda');
    Route::get('/quote/today',    [QuoteController::class, 'quoteOfTheDay'])           ->name('quote.today');
    Route::get('/articles',       [ArticleController::class, 'apiIndex'])              ->name('articles.index');

    // Chatbot API routes
    Route::controller(ChatbotController::class)->prefix('chatbot')->name('chatbot.')->group(function () {
        Route::post('/create-send',      'createSend') ->name('create-send');
        Route::post('/send/{identifier}', 'send')       ->name('send');
        Route::get('/stream/{identifier}', 'stream')     ->name('stream');
        Route::post('/save/{identifier}',  'saveMessage')->name('save');
    });

    // Tracker API routes
    Route::controller(TrackerController::class)->prefix('tracker')->name('tracker.')->group(function () {
        Route::get('/stats',         'getStats')       ->name('stats');
        Route::get('/weekly-stats',  'getWeeklyStats') ->name('weekly-stats');
        Route::get('/monthly-stats', 'getMonthlyStats')->name('monthly-stats');
    });

    // XP System API routes
    Route::controller(XpController::class)->prefix('xp')->name('xp.')->group(function () {
        Route::post('/award',                   'awardXp')              ->name('award');
        Route::get('/progress',                 'getXpProgress')        ->name('progress');
        Route::get('/daily-summary',            'getDailyXpSummary')    ->name('daily-summary');
        Route::get('/breakdown',                'getXpBreakdown')       ->name('breakdown');
        Route::get('/can-access-psychologist',  'canAccessPsychologist')->name('can-access-psychologist');
        Route::get('/history',                  'getXpHistory')         ->name('history');
    });

    // Share and Talk API routes
    Route::controller(ConsultationApiController::class)->prefix('share-and-talk')->name('share-and-talk.')->group(function () {
        Route::post('/cancel-session/{room}',   'cancelSession')         ->name('cancel-session');
        Route::post('/end-session/{room}',      'endSession')            ->name('end-session');
        Route::get('/session-status/{room}',    'getSessionStatus')      ->name('session-status');
        Route::post('/manual-activate/{room}',  'manualActivateSession') ->name('manual-activate');
        Route::get('/messages/{room}',          'getSessionMessages')    ->name('messages');
    });

    // Professional Availability and Schedule APIs
    Route::get('/professionals/{professional}/availability', [BookingController::class, 'getAvailabilitySlots'])->name('professionals.availability');
});
