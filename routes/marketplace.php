<?php

use App\Http\Controllers\MarketplaceController;
use Illuminate\Support\Facades\Route;

Route::controller(MarketplaceController::class)->prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{slug}', 'detail')->name('detail');
});
