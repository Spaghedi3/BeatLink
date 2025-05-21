<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserInteractionController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\PublicProfileController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : view('welcome');
})->name('welcome');

// Authentication Middleware Group
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/{username}', [PublicProfileController::class, 'show'])->name('profile.show');

    // Track Routes
    Route::get('/tracks', [TrackController::class, 'index'])->name('tracks.index');
    Route::get('/favorites', [TrackController::class, 'favorites'])->name('tracks.favorites');
    Route::post('/tracks', [TrackController::class, 'store'])->name('tracks.store');
    Route::get('/tracks/create', [TrackController::class, 'create'])->name('tracks.create');
    Route::get('/{username}/tracks', [TrackController::class, 'userTracks'])->name('user.tracks');
    Route::get('/tracks/{track}/edit', [TrackController::class, 'edit'])->name('tracks.edit');
    Route::put('/tracks/{track}', [TrackController::class, 'update'])->name('tracks.update');
    Route::get('/tracks/{track}/destroy', [TrackController::class, 'destroyConfirm'])->name('tracks.destroy.confirm');
    Route::delete('/tracks/{track}', [TrackController::class, 'destroy'])->name('tracks.destroy');
    Route::get('/tracks/check-name', [TrackController::class, 'checkName'])->name('tracks.check-name');
    Route::post('/react', [TrackController::class, 'react'])->name('reaction.react');

    // Notification Routes
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
    Route::get('/notifications/read/{notification}', [NotificationController::class, 'readAndRedirect'])
        ->name('notifications.read.one');

    // Interaction Routes
    Route::post('/interactions', [UserInteractionController::class, 'store'])
        ->name('interactions.store');

    // Recommendation Routes
    Route::get('/dashboard', [RecommendationController::class, 'recommend'])
        ->name('dashboard');
});

// Public track view
Route::get('/tracks/{track}', [TrackController::class, 'show'])->name('tracks.show');

require __DIR__ . '/auth.php';
