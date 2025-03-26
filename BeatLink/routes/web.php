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

// Beats index
Route::get('/beats', [BeatController::class, 'index'])->name('beats.index');

// Create route FIRST
Route::middleware('auth')->group(function () {
    Route::get('/beats/create', [BeatController::class, 'create'])->name('beats.create');
    Route::post('/beats', [BeatController::class, 'store'])->name('beats.store');
    Route::get('/beats/{beat}/edit', [BeatController::class, 'edit'])->name('beats.edit');
    Route::put('/beats/{beat}', [BeatController::class, 'update'])->name('beats.update');
    Route::get('/beats/{beat}/destroy', [BeatController::class, 'destroyConfirm'])->name('beats.destroy.confirm');
    Route::delete('/beats/{beat}', [BeatController::class, 'destroy'])->name('beats.destroy');
});

// Finally, the show route with the parameter
Route::get('/beats/{beat}', [BeatController::class, 'show'])->name('beats.show');


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
