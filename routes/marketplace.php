<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketplaceController;

Route::controller(MarketplaceController::class)->prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{slug}', 'detail')->name('detail');
});