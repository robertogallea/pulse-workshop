<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/fake-request', \App\Http\Controllers\FakeRequestController::class)
    ->name('fake-request');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
