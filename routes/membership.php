<?php

use App\Http\Controllers\MidtransWebhookController;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Route;

Route::get('/membership', function () {
    $plans = MembershipPlan::with('planBenefits')->get();

    return view('membership.index', compact('plans'));
})->name('membership.index')->middleware('auth');

Route::post('/api/midtrans/notification', [MidtransWebhookController::class, 'handle'])
    ->name('midtrans.notification');
