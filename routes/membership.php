<?php

use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\OrderController;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Route;

Route::get('/membership', function () {
    $plans = MembershipPlan::with('planBenefits')->get();

    return view('membership.index', compact('plans'));
})->name('membership.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/order/membership/{plan}', [OrderController::class, 'create'])->name('order.create');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/order/{order}/status', [OrderController::class, 'checkStatus'])->name('order.check-status');
});

Route::post('/api/midtrans/notification', [MidtransWebhookController::class, 'handle'])
    ->name('midtrans.notification');
