<?php

use App\Http\Controllers\DokuWebhookController;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Route;

Route::get('/membership', function () {
    $plans = MembershipPlan::with('planBenefits')->get();

    return view('membership.index', compact('plans'));
})->name('membership.index')->middleware('auth');

Route::post('/api/doku/notification', [DokuWebhookController::class, 'handle'])
    ->name('doku.notification');
