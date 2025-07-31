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
use App\Http\Controllers\XpController;
use App\Http\Controllers\XpRedemptionController;


Route::get('/', function () {
    return view('landing');
})->name('land');

Route::get('/portal', function () {
    return view('auth.login');
})->name('start');

Route::get('/dashboard', function () {
    $trackerController = new TrackerController();
    $statsData = $trackerController->getStatsForDashboard();
    return view('main.main', compact('statsData'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/terms-and-conditions', function () {
    return view('terms-and-conditions');
})->name('terms-and-conditions');

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/support-group-discussion', [SgdController::class, 'show'])->name('sgd');

    Route::get('/deep-cards', [CardController::class, 'index'])
        ->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':deep_cards');

    Route::get('/share-and-talk', [ShareAndTalkController::class, 'index'])->name('share-and-talk');
    Route::get('/share-and-talk/professionals', [ShareAndTalkController::class, 'getProfessionals']);
    Route::get('/share-and-talk/chat/{professionalId}', [ShareAndTalkController::class, 'chatConsultation'])
        ->middleware(\App\Http\Middleware\ShareAndTalkTicketGateMiddleware::class)
        ->name('share-and-talk.chat');
    Route::get('/share-and-talk/start-chat-session/{sessionId}', [ShareAndTalkController::class, 'startChatSession'])
        ->name('share-and-talk.start-chat-session');
    Route::get('/share-and-talk/chat-session/{sessionId}', [ShareAndTalkController::class, 'userChatSession'])
        ->name('share-and-talk.chat-session');
    Route::get('/share-and-talk/video/{professionalId}', [ShareAndTalkController::class, 'videoConsultation'])
        ->middleware(\App\Http\Middleware\ShareAndTalkVideoTicketGateMiddleware::class)
        ->name('share-and-talk.video');
    Route::get('/share-and-talk/start-video-session/{sessionId}', [ShareAndTalkController::class, 'startVideoSession'])
        ->name('share-and-talk.start-video-session');
    Route::get('/share-and-talk/video-session/{sessionId}', [ShareAndTalkController::class, 'userVideoSession'])
        ->name('share-and-talk.video-session');
    Route::post('/share-and-talk/chat/user-send', [ShareAndTalkController::class, 'userSend'])->name('share-and-talk.userSend');
    
    // Video consultation API endpoints
    Route::post('/api/share-and-talk/cancel-session/{sessionId}', [ShareAndTalkController::class, 'cancelSession'])->name('share-and-talk.cancel-session');
    Route::post('/api/share-and-talk/end-session/{sessionId}', [ShareAndTalkController::class, 'endSession'])->name('share-and-talk.end-session');

    Route::get('/mental-support-chatbot', [ChatbotController::class, 'index'])
        ->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':mentai_chatbot')
        ->name('chatbot');
    Route::get('/api/chatbot/sessions', [ChatbotController::class, 'getSessions'])->name('chatbot.get-sessions');
    Route::post('/api/chatbot/session', [ChatbotController::class, 'createSession'])->name('chatbot.create-session');
    Route::get('/api/chatbot/session/{sessionId}', [ChatbotController::class, 'getSession'])->name('chatbot.get-session');
    Route::delete('/api/chatbot/session/{sessionId}', [ChatbotController::class, 'deleteSession'])->name('chatbot.delete-session');
    Route::post('/api/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.api');

    Route::get('/tracker', [TrackerController::class,'index'])
        ->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':tracker')
        ->name('tracker.index');
    Route::post('tracker/track', [TrackerController::class,'track'])->name('tracker.entry');
    Route::get('/tracker/result', [TrackerController::class, 'result'])->name('tracker.result');
    Route::get('/tracker/history', [TrackerController::class,'history'])->name('tracker.history');
    Route::get('/tracker/stat/{id}', [TrackerController::class, 'showStat'])->name('tracker.stat.detail');
    Route::get('/tracker/weekly-stat/{id}', [TrackerController::class, 'showWeeklyStat'])
        ->middleware(\App\Http\Middleware\InnerPeaceMembershipMiddleware::class)
        ->name('tracker.weekly-stat.detail');
    Route::get('/tracker/monthly-stat/{id}', [TrackerController::class, 'showMonthlyStat'])
        ->middleware(\App\Http\Middleware\InnerPeaceMembershipMiddleware::class)
        ->name('tracker.monthly-stat.detail');

    Route::get('/api/tracker/stats', [TrackerController::class, 'getStats'])->name('api.tracker.stats');
    Route::get('/api/tracker/weekly-stats', [TrackerController::class, 'getWeeklyStats'])
        ->middleware(\App\Http\Middleware\InnerPeaceMembershipMiddleware::class)
        ->name('api.tracker.weekly-stats');
    Route::get('/api/tracker/monthly-stats', [TrackerController::class, 'getMonthlyStats'])
        ->middleware(\App\Http\Middleware\InnerPeaceMembershipMiddleware::class)
        ->name('api.tracker.monthly-stats');

    Route::get('mental-health-test', function () {
        return view('mental-test.form');
    })->name('mhc-sf.form');
    Route::post('/mental-test/submit', [MentalTestController::class, 'store'])->name('mental-test.store');

    Route::get('missions-of-the-day', function () {
        return view('missions');
    })
        ->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':missions')
        ->name('missions.index');

    Route::get('/membership', [\App\Http\Controllers\MembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/buy/{id}', [\App\Http\Controllers\MembershipController::class, 'buy'])->name('membership.buy');
    
    // XP System Routes
    Route::post('/api/xp/award', [XpController::class, 'awardXp'])->name('xp.award');
    Route::get('/api/xp/progress', [XpController::class, 'getXpProgress'])->name('xp.progress');
    Route::get('/api/xp/daily-summary', [XpController::class, 'getDailyXpSummary'])->name('xp.daily-summary');
    Route::get('/api/xp/breakdown', [XpController::class, 'getXpBreakdown'])->name('xp.breakdown');
    Route::get('/api/xp/can-access-psychologist', [XpController::class, 'canAccessPsychologist'])->name('xp.can-access-psychologist');
    Route::get('/api/xp/history', [XpController::class, 'getXpHistory'])->name('xp.history');

    // Test route for XP system (remove in production)
    Route::get('/test-xp', function() {
        $user = auth()->user();
        $xpService = app(\App\Services\XpService::class);
        
        return response()->json([
            'user_id' => $user->id,
            'total_xp' => $user->total_xp,
            'xp_progress' => $user->getXpProgress(),
            'daily_summary' => $user->getDailyXpSummary(),
            'xp_breakdown' => $user->getXpBreakdown(),
            'can_access_psychologist' => $user->canAccessPsychologist(),
            'membership_type' => $xpService->getUserMembershipType($user),
            'max_daily_xp' => $xpService->getMaxDailyXp($user)
        ]);
    })->name('test.xp');

    // XP Redemption Routes
    Route::get('/xp-redemption', [XpRedemptionController::class, 'index'])->name('xp-redemption.index');
    Route::post('/xp-redemption/redeem', [XpRedemptionController::class, 'redeem'])->name('xp-redemption.redeem');

});

require __DIR__.'/auth.php';


// API

Route::middleware('auth')->group(function () {
    Route::get('/user', [AuthenticatedSessionController::class, 'getUser'])->name('user.get');

    Route::get('/agenda/pending', [AgendaController::class, 'getPending'])->name('getPendingAgenda');
    Route::get('/quote/today', [QuoteController::class, 'quoteOfTheDay']);

    Route::get('/support-group-discussion/get', [SgdController::class, 'getGroups'])->name('group.get');
    Route::match(['GET', 'POST'], '/support-group-discussion/join', [SgdController::class, 'joinGroup'])
        ->middleware(\App\Http\Middleware\TicketGateMiddleware::class . ':support_group')
        ->name('group.join');
    // Route::get('/support-group-discussion/join/{id}', [SgdController::class, 'groupMeet'])->name('group.meet');
    Route::post('/support-group-discussion/enter-meeting', [SgdController::class, 'enterMeetingRoom'])->name('group.enter-meeting');
    Route::post('/support-group-discussion/leave', [SgdController::class, 'leaveGroup'])->name('group.leave');

    // SGD Payment Routes (Admin only)
    Route::get('/support-group-discussion/{groupId}/payment-data', [SgdController::class, 'getPaymentData'])->name('group.payment-data');
    Route::get('/support-group-discussion/{groupId}/consumption-details', [SgdController::class, 'getConsumptionDetails'])->name('group.consumption-details');
    Route::post('/support-group-discussion/payment-summary', [SgdController::class, 'getPaymentSummary'])->name('group.payment-summary');

    Route::get('/share-and-talk/messages/{sessionId}', [ShareAndTalkController::class, 'getMessages'])->name('share-and-talk.messages');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/missions-of-the-day', [MissionController::class, 'index'])->name('missions.index');
    Route::post('/missions-of-the-day/{mission}/complete', [MissionController::class, 'complete'])->name('missions.complete');
});


Route::get('/share-and-talk/facilitator/{sessionId}', [ShareAndTalkController::class, 'facilitatorChat'])->name('share-and-talk.facilitator');
Route::get('/share-and-talk/facilitator-video/{sessionId}', [ShareAndTalkController::class, 'facilitatorVideo'])->name('share-and-talk.facilitator-video');
Route::post('/share-and-talk/chat/facilitator-send', [ShareAndTalkController::class, 'facilitatorSend'])->name('share-and-talk.facilitatorSend');
Route::get('/api/share-and-talk/messages/{sessionId}', [ShareAndTalkController::class,'getMessages'])->name('share-and-talk.fetch');
Route::get('/api/share-and-talk/session-status/{sessionId}', [ShareAndTalkController::class, 'getSessionStatus']);
Route::post('/api/share-and-talk/cancel-session/{sessionId}', [ShareAndTalkController::class, 'cancelSessionByUser']);
Route::post('/api/share-and-talk/professional-online/{professionalId}', [ShareAndTalkController::class, 'setProfessionalOnline']);
Route::get('/share-and-talk/activate-session/{sessionId}', [ShareAndTalkController::class, 'activateSession'])->name('share-and-talk.activate-session');

// Professional Authentication Routes
Route::get('/professional/login', [\App\Http\Controllers\Auth\ProfessionalAuthenticatedSessionController::class, 'create'])->name('professional.login');
Route::post('/professional/login', [\App\Http\Controllers\Auth\ProfessionalAuthenticatedSessionController::class, 'store'])->name('professional.login');
Route::post('/professional/logout', [\App\Http\Controllers\Auth\ProfessionalAuthenticatedSessionController::class, 'destroy'])->name('professional.logout');

// Professional Dashboard Routes (Protected)
Route::middleware([\App\Http\Middleware\AuthenticateProfessional::class])->group(function () {
    Route::get('/professional/{professionalId}/dashboard', [\App\Http\Controllers\ProfessionalDashboardController::class, 'dashboard'])->name('professional.dashboard');
    Route::post('/professional/{professionalId}/update-availability', [\App\Http\Controllers\ProfessionalDashboardController::class, 'updateAvailability'])->name('professional.update-availability');
    Route::get('/professional/{professionalId}/availability', [\App\Http\Controllers\ProfessionalDashboardController::class, 'getAvailability'])->name('professional.get-availability');
    Route::post('/professional/{professionalId}/change-password', [\App\Http\Controllers\ProfessionalDashboardController::class, 'changePassword'])->name('professional.change-password');
    Route::post('/professional/logout', [\App\Http\Controllers\ProfessionalDashboardController::class, 'logout'])->name('professional.dashboard.logout');
});

Route::post('/mark-onboarding-completed', function () {
    if (auth()->check()) {
        auth()->user()->update(['onboarding_completed' => true]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 401);
})->middleware('auth');

Route::post('/reset-onboarding', function () {
    if (auth()->check()) {
        auth()->user()->update(['onboarding_completed' => false]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 401);
})->middleware('auth');

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