<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('movies', 'movies')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('favorites', 'favourites')
    ->middleware(['auth', 'verified'])
    ->name('favorites');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/tmdb/callback', function () {
    return redirect()->route('dashboard');
})
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
