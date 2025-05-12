<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\QuoteController;


Route::get('/', function () {
    return view('auth.login');
})->name('start');

Route::get('/dashboard', function () {
    return view('main.main');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// API

Route::middleware('auth')->group(function () {
    Route::get('/agenda/pending', [AgendaController::class, 'getPending'])->name('getPendingAgenda');
    Route::get('/quote/today', [QuoteController::class, 'quoteOfTheDay']);
});