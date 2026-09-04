<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\EClassChapterController;
use App\Http\Controllers\EClassController;
use App\Http\Controllers\QuizAttemptController;
use Illuminate\Support\Facades\Route;

Route::prefix('e-class')->name('e-class.')->group(function () {
    Route::get('/', [EClassController::class, 'index'])->name('index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/library', [EClassController::class, 'library'])->name('library');

        Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
        Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])
            ->name('certificates.download');
        Route::post('/certificates/{certificate}/email', [CertificateController::class, 'email'])
            ->middleware('throttle:3,1')
            ->name('certificates.email');

        Route::get('/quiz-attempts/{attempt}', [QuizAttemptController::class, 'show'])
            ->name('quiz-attempts.show');
        Route::post('/quiz-attempts/{attempt}/submit', [QuizAttemptController::class, 'submit'])
            ->middleware('throttle:10,1')
            ->name('quiz-attempts.submit');
    });

    Route::get('/{module}', [EClassController::class, 'show'])->name('show');

    Route::middleware(['auth', 'verified'])->scopeBindings()->group(function () {
        Route::post('/{module}/checkout', [EClassController::class, 'checkout'])
            ->middleware('throttle:5,1')
            ->name('checkout');
        Route::post('/{module}/certificates/claim', [CertificateController::class, 'claim'])
            ->middleware('throttle:5,1')
            ->name('certificates.claim');

        Route::get('/{module}/chapters/{chapter}', [EClassChapterController::class, 'show'])
            ->name('chapters.show');
        Route::get('/{module}/chapters/{chapter}/video', [EClassChapterController::class, 'video'])
            ->name('chapters.video');
        Route::post('/{module}/chapters/{chapter}/complete', [EClassChapterController::class, 'complete'])
            ->middleware('throttle:30,1')
            ->name('chapters.complete');
        Route::post('/{module}/chapters/{chapter}/attempts', [QuizAttemptController::class, 'start'])
            ->middleware('throttle:10,1')
            ->name('quiz-attempts.start');
    });
});
