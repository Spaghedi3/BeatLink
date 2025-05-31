<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserInteractionController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\vendor\Chatify\MessagesController;

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
    Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');


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

    // Chatify Main App Route
    Route::get('/messages', [MessagesController::class, 'index'])->name(config('chatify.routes.prefix'));

    // Chatify API-style Routes
    Route::post('/idInfo', [MessagesController::class, 'idFetchData']);
    Route::post('/sendMessage', [MessagesController::class, 'send'])->name('send.message');
    Route::post('/fetchMessages', [MessagesController::class, 'fetch'])->name('fetch.messages');
    Route::get('/download/{fileName}', [MessagesController::class, 'download'])->name(config('chatify.attachments.download_route_name'));
    Route::post('/chat/auth', [MessagesController::class, 'pusherAuth'])->name('pusher.auth');
    Route::post('/makeSeen', [MessagesController::class, 'seen'])->name('messages.seen');
    Route::get('/getContacts', [MessagesController::class, 'getContacts'])->name('contacts.get');
    Route::post('/updateContacts', [MessagesController::class, 'updateContactItem'])->name('contacts.update');
    Route::post('/star', [MessagesController::class, 'favorite'])->name('star');
    Route::post('/favorites', [MessagesController::class, 'getFavorites'])->name('favorites');
    Route::get('/search', [MessagesController::class, 'search'])->name('search');
    Route::post('/shared', [MessagesController::class, 'sharedPhotos'])->name('shared');
    Route::post('/deleteConversation', [MessagesController::class, 'deleteConversation'])->name('conversation.delete');
    Route::post('/deleteMessage', [MessagesController::class, 'deleteMessage'])->name('message.delete');
    Route::post('/updateSettings', [MessagesController::class, 'updateSettings'])->name('avatar.update');
    Route::post('/setActiveStatus', [MessagesController::class, 'setActiveStatus'])->name('activeStatus.set');

    // Special dynamic routes (at the end to avoid conflicts)
    Route::get('/group/{id}', [MessagesController::class, 'index'])
        ->name('chat.group');

    Route::get('/user/{id}', [MessagesController::class, 'index'])
        ->name('chat.user');
});

// Public track view
Route::get('/tracks/{track}', [TrackController::class, 'show'])->name('tracks.show');

require __DIR__ . '/auth.php';
