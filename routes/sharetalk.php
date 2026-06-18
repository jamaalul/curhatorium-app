<?php

use App\Http\Controllers\ShareAndTalk\BookingController;
use App\Http\Controllers\ShareAndTalk\ConsultationApiController;
use App\Http\Controllers\ShareAndTalk\ConsultationRoomController;
use Illuminate\Support\Facades\Route;

Route::prefix('share-and-talk')->name('share-and-talk.')->group(function () {

    // Booking Pages
    Route::get('/',                        [BookingController::class, 'index'])           ->name('index');
    Route::get('/professionals',           [BookingController::class, 'getProfessionals'])->name('professionals');
    Route::get('/waiting',                 [BookingController::class, 'wait'])            ->name('waiting');
    Route::get('/checkout/{professional}', [BookingController::class, 'showCheckoutPage'])->name('checkout');
    Route::get('/booked',                  [BookingController::class, 'booked'])          ->name('booked');
    Route::post('/book',                   [BookingController::class, 'bookSession'])     ->name('book');

    // Consultation Rooms
    Route::get('/chat/{room}',             [ConsultationRoomController::class, 'chatRoom']) ->name('chat.room');
    Route::get('/video/{room}',            [ConsultationRoomController::class, 'videoRoom'])->name('video.room');

    // Consultation API
    Route::post('/end',                    [ConsultationApiController::class, 'endSession'])  ->name('end');
    Route::post('/update-status',          [ConsultationApiController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/send-message',           [ConsultationApiController::class, 'clientSend'])  ->name('sendMessage');

})->middleware('auth');
