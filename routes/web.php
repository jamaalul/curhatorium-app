<?php

use App\Events\MessageSent;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ProfessionalAuthenticatedSessionController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MentalTestController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\ProfessionalDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PusherController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SgdController;
use App\Http\Controllers\ShareAndTalkController;
use App\Http\Controllers\TrackerController;
use App\Http\Controllers\XpController;
use App\Http\Controllers\XpRedemptionController;
use App\Http\Middleware\AuthenticateProfessional;
use App\Http\Middleware\InnerPeaceMembershipMiddleware;
use App\Http\Middleware\TicketGateMiddleware;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Publicly accessible routes
Route::get('/', fn () => view('landing'))->name('land');
Route::get('/portal', fn () => view('auth.login'))->name('start');
Route::get('/terms-and-conditions', fn () => view('terms-and-conditions'))->name('terms-and-conditions');
Route::get('/privacy-policy', fn () => view('privacy-policy'))->name('privacy-policy');

// Pusher routes for real-time messaging
Route::controller(PusherController::class)->prefix('pusher')->name('pusher.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/room/{room}', 'room')->name('room');
    Route::post('/room', 'createRoom')->name('createRoom');
    Route::post('/message', 'sendMessage')->name('sendMessage');
    Route::post('/terminate/{room}', 'terminate')->name('terminate');
});

// Public article routes
Route::controller(ArticleController::class)->prefix('articles')->name('articles.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{slug}', 'show')->name('show');
});

// Professional authentication routes
Route::name('professional.')->group(function () {
    Route::get('/professional/login', [ProfessionalAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/professional/login', [ProfessionalAuthenticatedSessionController::class, 'store']);
    Route::post('/professional/logout', [ProfessionalAuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Feature information pages
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


// Authenticated user routes
Route::middleware(['auth', 'verified'])->group(function () {
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
        return view('main.main', compact('statsData', 'announcement', 'user', 'cards'));
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

    // Membership routes
    Route::controller(MembershipController::class)->prefix('membership')->name('membership.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/buy/{id}', 'buy')->name('buy');
    });

    // Feature routes requiring authentication
    Route::get('/support-group-discussion', [SgdController::class, 'show'])->name('sgd');
    Route::get('/deep-cards', [CardController::class, 'index'])->middleware(TicketGateMiddleware::class . ':deep_cards');
    Route::get('/mental-support-chatbot', [ChatbotController::class, 'index'])->name('chatbot');
    Route::get('/mental-support-chatbot/{identifier}', [ChatbotController::class, 'chat'])->name('chatbot.chat');
    Route::get('mental-health-test', fn () => view('mental-test.form'))->name('mhc-sf.form');
    Route::post('/mental-test/submit', [MentalTestController::class, 'store'])->name('mental-test.store');

    // Share and Talk routes
    Route::controller(ShareAndTalkController::class)->prefix('share-and-talk')->name('share-and-talk.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/professionals', 'getProfessionals')->name('professionals');
        Route::get('/waiting', 'wait')->name('waiting');
        Route::get('/checkout/{professional}', 'showCheckoutPage')->name('checkout');
        Route::post('/book', 'bookSession')->name('book');
        Route::get('/booked', 'booked')->name('booked');
        Route::post('/end', 'endSession')->name('end');
        Route::get('/chat/{room}', 'chatRoom')->name('chat.room');
    });

    // Tracker routes
    Route::controller(TrackerController::class)->prefix('tracker')->name('tracker.')->group(function () {
        Route::get('/', 'index')->middleware(TicketGateMiddleware::class . ':tracker')->name('index');
        Route::post('/track', 'track')->middleware(TicketGateMiddleware::class . ':tracker')->name('entry');
        Route::get('/result', 'result')->name('result');
        Route::get('/history', 'history')->name('history');
        Route::get('/stat/{id}', 'showStat')->name('stat.detail');
        Route::get('/weekly-stat/{id}', 'showWeeklyStat')->middleware(InnerPeaceMembershipMiddleware::class)->name('weekly-stat.detail');
        Route::get('/monthly-stat/{id}', 'showMonthlyStat')->middleware(InnerPeaceMembershipMiddleware::class)->name('monthly-stat.detail');
    });

    // Missions routes
    Route::controller(MissionController::class)->prefix('missions-of-the-day')->name('missions.')->group(function () {
        Route::get('/', 'index')->middleware(TicketGateMiddleware::class . ':missions')->name('index');
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
        Route::match(['GET', 'POST'], '/join', 'joinGroup')->middleware(TicketGateMiddleware::class . ':support_group')->name('join');
        Route::post('/enter-meeting', 'enterMeetingRoom')->name('enter-meeting');
        Route::post('/leave', 'leaveGroup')->name('leave');
        // Admin-only SGD Payment Routes
        Route::get('/{groupId}/payment-data', 'getPaymentData')->name('payment-data');
        Route::get('/{groupId}/consumption-details', 'getConsumptionDetails')->name('consumption-details');
        Route::post('/payment-summary', 'getPaymentSummary')->name('payment-summary');
    });

    Route::get('/cards', [CardController::class, 'getCards'])->name('cards.all');
});

// API routes
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('/user', [AuthenticatedSessionController::class, 'getUser'])->name('user.get');
    Route::get('/agenda/pending', [AgendaController::class, 'getPending'])->name('getPendingAgenda');
    Route::get('/quote/today', [QuoteController::class, 'quoteOfTheDay'])->name('quote.today');
    Route::get('/articles', [ArticleController::class, 'apiIndex'])->name('articles.index');

    // Chatbot API routes
    Route::controller(ChatbotController::class)->prefix('chatbot')->name('chatbot.')->group(function () {
        Route::post('/create-send', [ChatbotController::class, 'createSend'])->name('create-send');
        Route::post('/send/{identifier}', [ChatbotController::class, 'send'])->name('send');
        Route::get('/stream/{identifier}', [ChatbotController::class, 'stream'])->name('stream');
        Route::post('/save/{identifier}', [ChatbotController::class, 'saveMessage'])->name('save');
    });

    // Tracker API routes
    Route::controller(TrackerController::class)->prefix('tracker')->name('tracker.')->group(function () {
        Route::get('/stats', 'getStats')->name('stats');
        Route::get('/weekly-stats', 'getWeeklyStats')->middleware(InnerPeaceMembershipMiddleware::class)->name('weekly-stats');
        Route::get('/monthly-stats', 'getMonthlyStats')->middleware(InnerPeaceMembershipMiddleware::class)->name('monthly-stats');
    });

    // XP System API routes
    Route::controller(XpController::class)->prefix('xp')->name('xp.')->group(function () {
        Route::post('/award', 'awardXp')->name('award');
        Route::get('/progress', 'getXpProgress')->name('progress');
        Route::get('/daily-summary', 'getDailyXpSummary')->name('daily-summary');
        Route::get('/breakdown', 'getXpBreakdown')->name('breakdown');
        Route::get('/can-access-psychologist', 'canAccessPsychologist')->name('can-access-psychologist');
        Route::get('/history', 'getXpHistory')->name('history');
    });

    // Share and Talk API routes
    Route::controller(ShareAndTalkController::class)->prefix('share-and-talk')->name('share-and-talk.')->group(function () {
        Route::post('/cancel-session/{sessionId}', 'cancelSession')->name('cancel-session');
        Route::post('/end-session/{sessionId}', 'endSession')->name('end-session');
    });

    // Professional Availability and Schedule APIs
    Route::get('/professionals/{professional}/availability', [ShareAndTalkController::class, 'getAvailabilitySlots'])->name('professionals.availability');
    Route::get('/professionals/{professional}/schedule', [ProfessionalDashboardController::class, 'getSchedule'])->name('professionals.schedule');
});

// Professional Dashboard routes (Protected)
Route::middleware([AuthenticateProfessional::class])->prefix('professional')->name('professional.')->group(function () {
    Route::get('/{professionalId}/dashboard', [ProfessionalDashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/availability/set', [ProfessionalDashboardController::class, 'setAvailability'])->name('set-availability');
    Route::post('/slots/{slot}/accept', [ProfessionalDashboardController::class, 'acceptBooking'])->name('booking.accept');
    Route::post('/slots/{slot}/decline', [ProfessionalDashboardController::class, 'declineBooking'])->name('booking.decline');
    Route::delete('/slots/{slot}', [ProfessionalDashboardController::class, 'deleteSlot'])->name('slot.delete');
    Route::post('/{professionalId}/change-password', [ProfessionalDashboardController::class, 'changePassword'])->name('change-password');
    Route::post('/logout', [ProfessionalDashboardController::class, 'logout'])->name('dashboard.logout');
});

require __DIR__ . '/auth.php';
