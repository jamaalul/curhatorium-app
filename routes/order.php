<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/order/membership/{plan}', [OrderController::class, 'create'])->name('order.create');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/order/{order}/status', [OrderController::class, 'checkStatus'])->name('order.check-status');
});