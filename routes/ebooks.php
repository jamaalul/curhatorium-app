<?php

use App\Http\Controllers\EbookController;
use Illuminate\Support\Facades\Route;

Route::prefix('ebooks')->name('ebooks.')->group(function () {
    Route::get('/', [EbookController::class, 'index'])->name('index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/library', [EbookController::class, 'library'])->name('library');
        Route::get('/{ebook}/read', [EbookController::class, 'read'])->name('read');
        Route::get('/{ebook}/stream', [EbookController::class, 'stream'])->name('stream')->middleware('signed');
        Route::get('/{ebook}/refresh-url', [EbookController::class, 'refreshUrl'])->name('refresh-url');
        Route::post('/{ebook}/progress', [EbookController::class, 'updateProgress'])->name('progress.update')->middleware('throttle:20,1');
        Route::post('/{ebook}/checkout', [EbookController::class, 'checkout'])->name('checkout');
        Route::post('/{ebook}/review', [EbookController::class, 'review'])->name('review');
    });

    Route::get('/{ebook}', [EbookController::class, 'show'])->name('show');
});
