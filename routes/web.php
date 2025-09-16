<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SgdController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ShareAndTalkController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\MentalTestController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\XpController;
use App\Http\Controllers\XpRedemptionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Publicly Accessible Routes
Route::get('/', fn () => view('landing'))->name('land');
Route::get('/portal', fn () => view('auth.login'))->name('start');
Route::get('/terms-and-conditions', fn () => view('terms-and-conditions'))->name('terms-and-conditions');
Route::get('/privacy-policy', fn () => view('privacy-policy'))->name('privacy-policy');

Route::get('/info/{feature}', function ($feature) {
    $featureData = [
        'mood-tracker' => [
            'title' => 'Mood and Productivity Tracker',
            'description' => 'A simple tool based on the Positive and Negative Affect Schedule (PANAS) to record your daily mood and productivity. Helps you recognize your emotional patterns over time. Small reflections can have a big impact.',
            'why_choose' => 'Track your emotional journey and understand your patterns to make better decisions for your mental well-being.',
            'cta' => 'Start Tracking Today',
            'cta_link' => '/tracker'
        ],
        'mental-health-test' => [
            'title' => 'Mental Health Test',
            'description' => 'A reflective test based on the Mental Health Continuum - Short Form (MHC-SF), designed to help you recognize your emotional, psychological, and social well-being. The results will provide a complete picture of your mental condition. Not for judgment, but for understanding.',
            'why_choose' => 'Gain insights into your mental health status and understand your emotional, psychological, and social well-being.',
            'cta' => 'Take the Test',
            'cta_link' => '/mental-test'
        ],
        'share-and-talk' => [
            'title' => 'Share and Talk',
            'description' => 'A personal storytelling space where you can choose to be with a Ranger (trained peer-support person) or a professional psychologist. You can share through chat or online face-to-face sessions. It\'s safe, anonymous, and without coercion.',
            'why_choose' => 'Get personalized support from trained professionals or peers in a safe, anonymous environment.',
            'cta' => 'Start Sharing',
            'cta_link' => '/share-and-talk'
        ],
        'ment-ai' => [
            'title' => 'Ment-AI',
            'description' => 'Sanny AI is ready to accompany you whenever you need it. It can help with reflection, breathing exercises, or simply accompany you when you\'re feeling down.',
            'why_choose' => 'Get 24/7 AI-powered support for reflection, breathing exercises, and emotional companionship.',
            'cta' => 'Chat with AI',
            'cta_link' => '/chatbot'
        ],
        'missions' => [
            'title' => 'Missions of the Day',
            'description' => 'Simple daily missions that help you reconnect with yourself. Each mission can be a reflection, a small action, or a light exercise. Choose the level that suits your daily rhythm.',
            'why_choose' => 'Stay motivated with simple daily activities that help you reconnect with yourself and maintain mental wellness.',
            'cta' => 'View Missions',
            'cta_link' => '/missions'
        ],
        'support-group' => [
            'title' => 'Support Group Discussion',
            'description' => 'Reflective discussions in small groups, guided by a Ranger. You can share your story or just listen. Everything is anonymous, and everyone understands each other.',
            'why_choose' => 'Connect with others who understand your journey in a safe, anonymous group setting.',
            'cta' => 'Join Group',
            'cta_link' => '/sgd'
        ],
        'deep-cards' => [
            'title' => 'Deep Cards',
            'description' => 'Reflection cards containing deep questions. Draw a card and write down what\'s in your heart. There are no right or wrong answers, only space to be honest with yourself.',
            'why_choose' => 'Explore your thoughts and feelings through guided reflection questions in a judgment-free space.',
            'cta' => 'Draw a Card',
            'cta_link' => '/deep-cards'
        ]
    ];

    if (!isset($featureData[$feature])) {
        abort(404);
    }

    return view('info.feature', ['feature' => $featureData[$feature]]);
})->name('info.feature');


// Public Articles
Route::controller(ArticleController::class)->prefix('articles')->as('articles.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{slug}', 'show')->name('show');
});
Route::get('/api/articles', [ArticleController::class, 'apiIndex'])->name('api.articles.index');


// Authenticated User Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $trackerController = app(TrackerController::class);
        $statsData = $trackerController->getStatsForDashboard();
        $announcement = \App\Models\Announcement::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->latest('starts_at')
            ->first();
        return view('main.main', compact('statsData', 'announcement', 'user'));
    })->middleware('verified')->name('dashboard');

    // Profile
    Route::controller(ProfileController::class)->prefix('profile')->as('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->middleware('profile.upload.limit')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // Onboarding
    Route::post('/mark-onboarding-completed', function () {
        if (Auth::check()) {
            Auth::user()->update(['onboarding_completed' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    })->middleware('auth');

    Route::post('/reset-onboarding', function () {
        if (Auth::check()) {
            Auth::user()->update(['onboarding_completed' => false]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 401);
    })->middleware('auth');

    // Core Features
    Route::get('/support-group-discussion', [SgdController::class, 'show'])->name('sgd');
    Route::get('/deep-cards', [CardController::class, 'index'])->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':deep_cards');
    Route::get('/mental-support-chatbot', [ChatbotController::class, 'index'])->name('chatbot');
    Route::get('mental-health-test', fn () => view('mental-test.form'))->name('mhc-sf.form');
    Route::post('/mental-test/submit', [MentalTestController::class, 'store'])->name('mental-test.store');
    Route::get('/membership', [\App\Http\Controllers\MembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/buy/{id}', [\App\Http\Controllers\MembershipController::class, 'buy'])->name('membership.buy');

    // Missions
    Route::controller(MissionController::class)->prefix('missions-of-the-day')->as('missions.')->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':missions')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{mission}/complete', 'complete')->name('complete');
    });

    // Share and Talk
    Route::controller(ShareAndTalkController::class)->prefix('share-and-talk')->as('share-and-talk.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/professionals', 'getProfessionals');
        Route::get('/chat/{professionalId}', 'chatConsultation')->middleware(\App\Http\Middleware\ShareAndTalkTicketGateMiddleware::class)->name('chat');
        Route::get('/start-chat-session/{sessionId}', 'startChatSession')->name('start-chat-session');
        Route::get('/chat-session/{sessionId}', 'userChatSession')->name('chat-session');
        Route::get('/video/{professionalId}', 'videoConsultation')->middleware(\App\Http\Middleware\ShareAndTalkVideoTicketGateMiddleware::class)->name('video');
        Route::get('/start-video-session/{sessionId}', 'startVideoSession')->name('start-video-session');
        Route::get('/video-session/{sessionId}', 'userVideoSession')->name('video-session');
        Route::post('/chat/user-send', 'userSend')->name('userSend');
        Route::get('/messages/{sessionId}', 'getMessages')->name('messages');
        Route::get('/facilitator/{sessionId}', 'facilitatorChat')->name('facilitator');
        Route::get('/facilitator-video/{sessionId}', 'facilitatorVideo')->name('facilitator-video');
        Route::post('/chat/facilitator-send', 'facilitatorSend')->name('facilitatorSend');
        Route::get('/activate-session/{sessionId}', 'activateSession')->name('activate-session');
    });

    // Tracker
    Route::controller(TrackerController::class)->prefix('tracker')->as('tracker.')->group(function () {
        Route::get('/', 'index')->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':tracker')->name('index');
        Route::post('/track', 'track')->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':tracker')->name('entry');
        Route::get('/result', 'result')->name('result');
        Route::get('/history', 'history')->name('history');
        Route::get('/stat/{id}', 'showStat')->name('stat.detail');
        Route::middleware(\App\Http\Middleware\InnerPeaceMembershipMiddleware::class)->group(function () {
            Route::get('/weekly-stat/{id}', 'showWeeklyStat')->name('weekly-stat.detail');
            Route::get('/monthly-stat/{id}', 'showMonthlyStat')->name('monthly-stat.detail');
        });
    });

    // XP System
    Route::controller(XpController::class)->prefix('api/xp')->as('xp.')->group(function () {
        Route::post('/award', 'awardXp')->name('award');
        Route::get('/progress', 'getXpProgress')->name('progress');
        Route::get('/daily-summary', 'getDailyXpSummary')->name('daily-summary');
        Route::get('/breakdown', 'getXpBreakdown')->name('breakdown');
        Route::get('/can-access-psychologist', 'canAccessPsychologist')->name('can-access-psychologist');
        Route::get('/history', 'getXpHistory')->name('history');
    });

    // XP Redemption
    Route::controller(XpRedemptionController::class)->prefix('xp-redemption')->as('xp-redemption.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/redeem', 'redeem')->name('redeem');
    });

    // Misc API
    Route::get('/cards', [CardController::class, 'getCards'])->name('cards.all');
    Route::get('/user', [AuthenticatedSessionController::class, 'getUser'])->name('user.get');
    Route::get('/agenda/pending', [AgendaController::class, 'getPending'])->name('getPendingAgenda');
    Route::get('/quote/today', [QuoteController::class, 'quoteOfTheDay']);

    // API Routes
    Route::prefix('api')->as('api.')->group(function () {
        // Chatbot API
        Route::controller(ChatbotController::class)->prefix('chatbot')->as('chatbot.')->group(function () {
            Route::get('/sessions', 'getSessions')->name('get-sessions');
            Route::post('/session', 'createSession')->name('create-session');
            Route::get('/session/{sessionId}', 'getSession')->name('get-session');
            Route::delete('/session/{sessionId}', 'deleteSession')->name('delete-session');
            Route::post('/', 'chat')->name('api');
        });

        // Tracker API
        Route::controller(TrackerController::class)->prefix('tracker')->as('tracker.')->group(function () {
            Route::get('/stats', 'getStats')->name('stats');
            Route::middleware(\App\Http\Middleware\InnerPeaceMembershipMiddleware::class)->group(function () {
                Route::get('/weekly-stats', 'getWeeklyStats')->name('weekly-stats');
                Route::get('/monthly-stats', 'getMonthlyStats')->name('monthly-stats');
            });
        });

        // Share and Talk API
        Route::controller(ShareAndTalkController::class)->prefix('share-and-talk')->as('share-and-talk.')->group(function () {
            Route::post('/cancel-session/{sessionId}', 'cancelSession')->name('cancel-session');
            Route::post('/end-session/{sessionId}', 'endSession')->name('end-session');
            Route::get('/messages/{sessionId}', 'getMessages')->name('fetch');
            Route::get('/session-status/{sessionId}', 'getSessionStatus');
            Route::post('/cancel-session/{sessionId}', 'cancelSessionByUser');
            Route::post('/professional-online/{professionalId}', 'setProfessionalOnline');
            Route::post('/manual-activate/{sessionId}', 'manualActivateSession')->name('manual-activate');
        });

        // Support Group Discussion API
        Route::controller(SgdController::class)->prefix('support-group-discussion')->as('group.')->group(function () {
            Route::get('/get', 'getGroups')->name('get');
            Route::match(['GET', 'POST'], '/join', 'joinGroup')->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':support_group')->name('join');
            Route::post('/enter-meeting', 'enterMeetingRoom')->name('enter-meeting');
            Route::post('/leave', 'leaveGroup')->name('leave');
            // Admin-only payment routes
            Route::get('/{groupId}/payment-data', 'getPaymentData')->name('payment-data');
            Route::get('/{groupId}/consumption-details', 'getConsumptionDetails')->name('consumption-details');
            Route::post('/payment-summary', 'getPaymentSummary')->name('payment-summary');
        });
    });
});

// Professional Routes
Route::prefix('professional')->as('professional.')->group(function () {
    // Authentication
    Route::controller(\App\Http\Controllers\Auth\ProfessionalAuthenticatedSessionController::class)->group(function () {
        Route::get('/login', 'create')->name('login');
        Route::post('/login', 'store');
        Route::post('/logout', 'destroy')->name('logout');
    });

    // Dashboard & Actions (Protected)
    Route::middleware(\App\Http\Middleware\AuthenticateProfessional::class)->group(function () {
        Route::controller(\App\Http\Controllers\ProfessionalDashboardController::class)->group(function () {
            Route::get('/{professionalId}/dashboard', 'dashboard')->name('dashboard');
            Route::post('/{professionalId}/update-availability', 'updateAvailability')->name('update-availability');
            Route::get('/{professionalId}/availability', 'getAvailability')->name('get-availability');
            Route::post('/{professionalId}/change-password', 'changePassword')->name('change-password');
            Route::post('/logout', 'logout')->name('dashboard.logout');
        });
    });
});


require __DIR__.'/auth.php';

