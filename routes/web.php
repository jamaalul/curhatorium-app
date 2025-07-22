<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SgdController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ShareAndTalkController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\MentalTestController;
use App\Http\Controllers\MissionController;


Route::get('/', function () {
    return view('auth.login');
})->name('start');

Route::get('/dashboard', function () {
    $trackerController = new \App\Http\Controllers\TrackerController();
    $statsData = $trackerController->getStatsForDashboard();
    return view('main.main', compact('statsData'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/support-group-discussion', [SgdController::class, 'show'])->name('sgd');

    Route::get('/deep-cards', [CardController::class, 'index']);

    Route::get('/share-and-talk', [ShareAndTalkController::class, 'index'])->name('share-and-talk');
    Route::get('/share-and-talk/professionals', [ShareAndTalkController::class, 'getProfessionals']);
    Route::get('/share-and-talk/chat/{professionalId}', [ShareAndTalkController::class, 'chatConsultation'])->name('share-and-talk.chat');
    Route::post('/share-and-talk/chat/user-send', [ShareAndTalkController::class, 'userSend'])->name('share-and-talk.userSend');

    Route::get('/mental-support-chatbot', [ChatbotController::class, 'index'])->name('chatbot');
    Route::get('/api/chatbot/sessions', [ChatbotController::class, 'getSessions'])->name('chatbot.get-sessions');
    Route::post('/api/chatbot/session', [ChatbotController::class, 'createSession'])->name('chatbot.create-session');
    Route::get('/api/chatbot/session/{sessionId}', [ChatbotController::class, 'getSession'])->name('chatbot.get-session');
    Route::delete('/api/chatbot/session/{sessionId}', [ChatbotController::class, 'deleteSession'])->name('chatbot.delete-session');
    Route::post('/api/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.api');

    Route::get('/tracker', [TrackerController::class,'index'])->name('tracker.index');
    Route::post('tracker/track', [TrackerController::class,'track'])->name('tracker.entry');
    Route::get('/tracker/result', [TrackerController::class, 'result'])->name('tracker.result');
    Route::get('/tracker/history', [TrackerController::class,'history'])->name('tracker.history');
    Route::get('/tracker/stat/{id}', [TrackerController::class, 'showStat'])->name('tracker.stat.detail');
    Route::get('/tracker/weekly-stat/{id}', [TrackerController::class, 'showWeeklyStat'])->name('tracker.weekly-stat.detail');
    Route::get('/tracker/monthly-stat/{id}', [TrackerController::class, 'showMonthlyStat'])->name('tracker.monthly-stat.detail');

    Route::get('/api/tracker/stats', [TrackerController::class, 'getStats'])->name('api.tracker.stats');
    Route::get('/api/tracker/weekly-stats', [TrackerController::class, 'getWeeklyStats'])->name('api.tracker.weekly-stats');
    Route::get('/api/tracker/monthly-stats', [TrackerController::class, 'getMonthlyStats'])->name('api.tracker.monthly-stats');

    Route::get('mental-health-test', function () {
        return view('mental-test.form');
    })->name('mhc-sf.form');
    Route::post('/mental-test/submit', [MentalTestController::class, 'store'])->name('mental-test.store');

    Route::get('missions-of-the-day', function () {
        return view('missions');
    })->name('missions.index');

    Route::get('/membership', function () {
        return view('membership.index');
    })->name('membership.index');
});

require __DIR__.'/auth.php';


// API

Route::middleware('auth')->group(function () {
    Route::get('/user', [AuthenticatedSessionController::class, 'getUser'])->name('user.get');

    Route::get('/agenda/pending', [AgendaController::class, 'getPending'])->name('getPendingAgenda');
    Route::get('/quote/today', [QuoteController::class, 'quoteOfTheDay']);

    Route::get('/support-group-discussion/get', [SgdController::class, 'getGroups'])->name('group.get');
    Route::post('/support-group-discussion/join', [SgdController::class, 'joinGroup'])->name('group.join');
    // Route::get('/support-group-discussion/join/{id}', [SgdController::class, 'groupMeet'])->name('group.meet');
    Route::post('/support-group-discussion/enter-meeting', [SgdController::class, 'enterMeetingRoom'])->name('group.enter-meeting');
    Route::post('/support-group-discussion/leave', [SgdController::class, 'leaveGroup'])->name('group.leave');

    Route::get('/share-and-talk/messages/{sessionId}', [ShareAndTalkController::class, 'getMessages'])->name('share-and-talk.messages');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/missions-of-the-day', [MissionController::class, 'index'])->name('missions.index');
    Route::post('/missions-of-the-day/{mission}/complete', [MissionController::class, 'complete'])->name('missions.complete');
});


Route::get('/share-and-talk/facilitator/{sessionId}', [ShareAndTalkController::class, 'facilitatorChat'])->name('share-and-talk.facilitator');
Route::post('/share-and-talk/chat/facilitator-send', [ShareAndTalkController::class, 'facilitatorSend'])->name('share-and-talk.facilitatorSend');
Route::get('/api/share-and-talk/messages/{sessionId}', [ShareAndTalkController::class,'getMessages'])->name('share-and-talk.fetch');