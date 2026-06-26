<?php

use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Route;

// Publicly accessible routes
Route::get('/', function () {
    $plans = MembershipPlan::with('planBenefits')->get();

    return view('landing', compact('plans'));
})->name('land');
Route::get('/portal', fn () => view('auth.login'))->name('start');
Route::get('/terms-and-conditions', fn () => view('terms-and-conditions'))->name('terms-and-conditions');
Route::get('/privacy-policy', fn () => view('privacy-policy'))->name('privacy-policy');

// Feature information pages
Route::get('/info/{feature}', function ($feature) {
    $featureData = [
        'mood-tracker' => [
            'title' => 'Mood and Productivity Tracker',
            'description' => 'A simple tool based on the Positive and Negative Affect Schedule (PANAS) to record your daily mood and productivity. Helps you recognize your emotional patterns over time. Small reflections can have a big impact.',
            'why_choose' => 'Track your emotional journey and understand your patterns to make better decisions for your mental well-being.',
            'cta' => 'Start Tracking Today',
            'cta_link' => '/tracker',
        ],
        'mental-health-test' => [
            'title' => 'Mental Health Test',
            'description' => 'A reflective test based on the Mental Health Continuum - Short Form (MHC-SF), designed to help you recognize your emotional, psychological, and social well-being. The results will provide a complete picture of your mental condition. Not for judgment, but for understanding.',
            'why_choose' => 'Gain insights into your mental health status and understand your emotional, psychological, and social well-being.',
            'cta' => 'Take the Test',
            'cta_link' => '/mental-test',
        ],
        'share-and-talk' => [
            'title' => 'Share and Talk',
            'description' => 'A personal storytelling space where you can choose to be with a Ranger (trained peer-support person) or a professional psychologist. You can share through chat or online face-to-face sessions. It\'s safe, anonymous, and without coercion.',
            'why_choose' => 'Get personalized support from trained professionals or peers in a safe, anonymous environment.',
            'cta' => 'Start Sharing',
            'cta_link' => '/share-and-talk',
        ],
        'ment-ai' => [
            'title' => 'Ment-AI',
            'description' => 'Sanny AI is ready to accompany you whenever you need it. It can help with reflection, breathing exercises, or simply accompany you when you\'re feeling down.',
            'why_choose' => 'Get 24/7 AI-powered support for reflection, breathing exercises, and emotional companionship.',
            'cta' => 'Chat with AI',
            'cta_link' => '/chatbot',
        ],
        'missions' => [
            'title' => 'Missions of the Day',
            'description' => 'Simple daily missions that help you reconnect with yourself. Each mission can be a reflection, a small action, or a light exercise. Choose the level that suits your daily rhythm.',
            'why_choose' => 'Stay motivated with simple daily activities that help you reconnect with yourself and maintain mental wellness.',
            'cta' => 'View Missions',
            'cta_link' => '/missions',
        ],
        'support-group' => [
            'title' => 'Support Group Discussion',
            'description' => 'Reflective discussions in small groups, guided by a Ranger. You can share your story or just listen. Everything is anonymous, and everyone understands each other.',
            'why_choose' => 'Connect with others who understand your journey in a safe, anonymous group setting.',
            'cta' => 'Join Group',
            'cta_link' => '/sgd',
        ],
        'deep-cards' => [
            'title' => 'Deep Cards',
            'description' => 'Reflection cards containing deep questions. Draw a card and write down what\'s in your heart. There are no right or wrong answers, only space to be honest with yourself.',
            'why_choose' => 'Explore your thoughts and feelings through guided reflection questions in a judgment-free space.',
            'cta' => 'Draw a Card',
            'cta_link' => '/deep-cards',
        ],
    ];

    if (! isset($featureData[$feature])) {
        abort(404);
    }

    return view('info.feature', ['feature' => $featureData[$feature]]);
})->name('info.feature');

require __DIR__.'/auth.php';
require __DIR__.'/dashboard.php';
require __DIR__.'/api_web.php';
require __DIR__.'/membership.php';
require __DIR__.'/order.php';
require __DIR__.'/articles.php';
require __DIR__.'/moodtracker.php';
require __DIR__.'/professional.php';
require __DIR__.'/sharetalk.php';
require __DIR__.'/marketplace.php';
