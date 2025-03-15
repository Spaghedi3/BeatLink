<?php

use App\Http\Controllers\ProfileController;
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

///TO DO
Route::get('/beats', function () {
    if (Auth::check()) {
        return redirect()->route('beats');
    }
});

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
