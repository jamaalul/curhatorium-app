<?php

use App\Http\Controllers\Auth\ProfessionalAuthenticatedSessionController;
use App\Http\Controllers\ProfessionalDashboardController;
use App\Http\Controllers\RescheduleController;
use App\Http\Controllers\ShareAndTalk\ConsultationApiController;
use App\Http\Controllers\ShareAndTalk\ConsultationRoomController;
use App\Http\Middleware\AuthenticateProfessional;
use Illuminate\Support\Facades\Route;

// Professional auth routes (login/logout)
Route::controller(ProfessionalAuthenticatedSessionController::class)->prefix('professional')->name('professional.')->group(function () {
    Route::get('/login',     'create') ->name('login');
    Route::post('/login',    'store');
    Route::post('/logout',   'destroy')->name('logout');
});

// Professional Dashboard routes (Protected)
Route::middleware([AuthenticateProfessional::class])->prefix('professional')->name('professional.')->group(function () {
    Route::get('/{professionalId}/dashboard',                               [ProfessionalDashboardController::class, 'dashboard'])           ->name('dashboard');
    Route::post('/{professionalId}/availability/set',                       [ProfessionalDashboardController::class, 'setAvailability'])     ->name('set-availability');
    Route::post('/slots/{slot}/accept',                                     [ProfessionalDashboardController::class, 'acceptBooking'])       ->name('booking.accept');
    Route::post('/slots/{slot}/decline',                                    [ProfessionalDashboardController::class, 'declineBooking'])      ->name('booking.decline');
    Route::post('/slots/{slot}/reschedule',                                 [ProfessionalDashboardController::class, 'rescheduleBooking'])   ->name('booking.reschedule');
    Route::get('/{professionalId}/reschedule/{rescheduleId}/offer-slots',   [ProfessionalDashboardController::class, 'showOfferSlotsForm'])  ->name('reschedule.offer-slots');
    Route::post('/{professionalId}/reschedule/{rescheduleId}/offer-slots',  [ProfessionalDashboardController::class, 'offerRescheduleSlots'])->name('reschedule.offer-slots.save');
    Route::get('/{professionalId}/reschedules',                             [ProfessionalDashboardController::class, 'listReschedules'])     ->name('reschedule.list');
    Route::get('/chat/{room}',                                              [ConsultationRoomController::class,      'chatRoom'])            ->name('chat.room');
    Route::get('/video/{room}',                                             [ConsultationRoomController::class,      'videoRoom'])           ->name('video.room');
    Route::post('/update-status',                                           [ConsultationApiController::class,       'updateStatus'])        ->name('updateStatus');
    Route::post('/send-message',                                            [ConsultationApiController::class,       'facilitatorSend'])     ->name('sendMessage');
    Route::delete('/slots/{slot}',                                          [ProfessionalDashboardController::class, 'deleteSlot'])          ->name('slot.delete');
    Route::post('/{professionalId}/change-password',                        [ProfessionalDashboardController::class, 'changePassword'])      ->name('change-password');
    Route::post('/logout',                                                  [ProfessionalDashboardController::class, 'logout'])              ->name('dashboard.logout');
});

// Client Public Routes (No Auth Required)
Route::get('/reschedule/{token}',        [RescheduleController::class, 'clientInterface'])->name('reschedule.client');
Route::post('/reschedule/{token}/select', [RescheduleController::class, 'selectSlot'])     ->name('reschedule.select');

// Professional Schedule API (no auth required for professionals)
Route::get('/api/professionals/{professional}/schedule', [ProfessionalDashboardController::class, 'getSchedule'])->name('api.professionals.schedule');
