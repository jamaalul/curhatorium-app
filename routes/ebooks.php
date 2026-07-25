<?php

use App\Http\Controllers\EbookController;
use Illuminate\Support\Facades\Route;

Route::controller(EbookController::class)->prefix('ebooks')->name('ebooks.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{ebook}', 'show')->name('show');
    Route::post('/{ebook}/checkout', 'checkout')
        ->middleware(['auth', 'verified'])
        ->name('checkout');
});
