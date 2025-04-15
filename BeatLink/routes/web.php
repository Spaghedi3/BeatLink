<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BeatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        // Redirect logged-in users to the dashboard or "for-you" page
        return redirect()->route('dashboard');
    }

    // Show the welcome page for guests
    return view('welcome');
})->name('welcome');

use App\Http\Controllers\TrackController;

// Tracks index
Route::get('/tracks', [TrackController::class, 'index'])->name('tracks.index');

// Create route FIRST
Route::middleware('auth')->group(function () {
    Route::put('/tracks/{track}', [TrackController::class, 'update'])->name('tracks.update');
    Route::post('/tracks', [TrackController::class, 'store'])->name('tracks.store');
    Route::get('/tracks/create', [TrackController::class, 'create'])->name('tracks.create');
    Route::get('/{username}/tracks', [TrackController::class, 'userTracks'])->name('user.tracks');
    Route::get('/tracks/{track}/edit', [TrackController::class, 'edit'])->name('tracks.edit');
    Route::get('/tracks/{track}/destroy', [TrackController::class, 'destroyConfirm'])->name('tracks.destroy.confirm');
    Route::get('/tracks/check-name', [TrackController::class, 'checkName'])->name('tracks.check-name');
    Route::delete('/tracks/{track}', [TrackController::class, 'destroy'])->name('tracks.destroy');
});

// Finally, the show route with the parameter
Route::get('/tracks/{track}', [TrackController::class, 'show'])->name('tracks.show');



///TO DO
Route::get('/links', function () {
    if (Auth::check()) {
        return redirect()->route('links');
    }
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
