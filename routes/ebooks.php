<?php

use App\Http\Controllers\EbookController;
use Illuminate\Support\Facades\Route;

Route::controller(EbookController::class)->prefix('ebooks')->name('ebooks.')->group(function () {
    Route::get('/', 'index')->name('index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/library', 'library')->name('library');
        Route::get('/{ebook}/read', 'read')->name('read');
        Route::get('/{ebook}/stream', 'stream')->name('stream')->middleware('signed');
        Route::get('/{ebook}/refresh-url', 'refreshUrl')->name('refresh-url');
        Route::post('/{ebook}/progress', 'updateProgress')->name('progress.update')->middleware('throttle:20,1');
    });

    Route::get('/{ebook}', 'show')->name('show');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::post('/{ebook}/checkout', 'checkout')->name('checkout');
        Route::post('/{ebook}/review', 'review')->name('review');
    });
});
