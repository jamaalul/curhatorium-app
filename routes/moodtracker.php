<?php

use App\Http\Controllers\TrackerController;
use Illuminate\Support\Facades\Route;

Route::controller(TrackerController::class)->prefix('tracker')->name('tracker.')->group(function () {
    Route::get('/',                   'index')          ->name('index');
    Route::post('/track',             'track')          ->name('entry');
    Route::get('/result',             'result')         ->name('result');
    Route::get('/history',            'history')        ->name('history');
    Route::get('/stat/{id}',          'showStat')       ->name('stat.detail');
    Route::get('/weekly-stat/{id}',   'showWeeklyStat') ->name('weekly-stat.detail');
    Route::get('/monthly-stat/{id}',  'showMonthlyStat')->name('monthly-stat.detail');
})->middleware('auth');
