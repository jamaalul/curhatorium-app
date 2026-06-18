<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\MentalTestController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SgdController;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\XpRedemptionController;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $trackerController = app(TrackerController::class);
        $statsData = $trackerController->getStatsForDashboard();
        $announcement = Announcement::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->latest('starts_at')
            ->first();
        $user = Auth::user();
        $cards = []; // Cards are now loaded via JavaScript
        $hasCalmStarter = false;
        $hasEverHadCalmStarter = false;

        return view('main.main', compact('statsData', 'announcement', 'user', 'cards', 'hasCalmStarter', 'hasEverHadCalmStarter'));
    })->name('dashboard');

    // Profile routes
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->middleware('profile.upload.limit')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // Onboarding routes
    Route::post('/mark-onboarding-completed', function () {
        Auth::user()->update(['onboarding_completed' => true]);

        return response()->json(['success' => true]);
    })->name('onboarding.complete');

    Route::post('/reset-onboarding', function () {
        Auth::user()->update(['onboarding_completed' => false]);

        return response()->json(['success' => true]);
    })->name('onboarding.reset');

    // Feature routes requiring authentication
    Route::get('/support-group-discussion', [SgdController::class, 'show'])->name('sgd');
    Route::get('/deep-cards', [CardController::class, 'index']);
    Route::get('/mental-support-chatbot', [ChatbotController::class, 'index'])->name('chatbot');
    Route::get('/mental-support-chatbot/{identifier}', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::get('mental-health-test', fn () => view('mental-test.form'))->name('mhc-sf.form');
    Route::post('/mental-test/submit', [MentalTestController::class, 'store'])->name('mental-test.store');

    // Missions routes
    Route::controller(MissionController::class)->prefix('missions-of-the-day')->name('missions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{mission}/complete', 'complete')->name('complete');
    });

    // XP Redemption routes
    Route::controller(XpRedemptionController::class)->prefix('xp-redemption')->name('xp-redemption.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/redeem', 'redeem')->name('redeem');
    });

    // Support Group Discussion routes
    Route::controller(SgdController::class)->prefix('support-group-discussion')->name('group.')->group(function () {
        Route::get('/get', 'getGroups')->name('get');
        Route::match(['GET', 'POST'], '/join', 'joinGroup')->name('join');
        Route::post('/enter-meeting', 'enterMeetingRoom')->name('enter-meeting');
        Route::post('/leave', 'leaveGroup')->name('leave');
        // Admin-only SGD Payment Routes
        Route::get('/{groupId}/payment-data', 'getPaymentData')->name('payment-data');
        Route::get('/{groupId}/consumption-details', 'getConsumptionDetails')->name('consumption-details');
        Route::post('/payment-summary', 'getPaymentSummary')->name('payment-summary');
    });

    Route::get('/cards', [CardController::class, 'getCards'])->name('cards.all');
    Route::get('/quote/today', [QuoteController::class, 'quoteOfTheDay']);
    Route::get('/user', [AuthenticatedSessionController::class, 'getUser']);
});
