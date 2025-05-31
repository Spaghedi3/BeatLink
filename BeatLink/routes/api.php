<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\vendor\Chatify\Api\MessagesController;

// Grouped API routes for Chatify (protected by API auth middleware)
Route::middleware('auth:sanctum')->prefix('chatify')->group(function () {
    Route::post('/sendMessage', [MessagesController::class, 'send'])->name('api.chatify.send');
    Route::post('/fetchMessages', [MessagesController::class, 'fetch'])->name('api.chatify.fetch');
    Route::post('/makeSeen', [MessagesController::class, 'seen'])->name('api.chatify.seen');
    Route::get('/getContacts', [MessagesController::class, 'getContacts'])->name('api.chatify.contacts');
    Route::post('/idInfo', [MessagesController::class, 'idFetchData'])->name('api.chatify.idInfo');
    Route::post('/star', [MessagesController::class, 'favorite'])->name('api.chatify.favorite');
    Route::post('/favorites', [MessagesController::class, 'getFavorites'])->name('api.chatify.favorites');
    Route::get('/search', [MessagesController::class, 'search'])->name('api.chatify.search');
    Route::post('/shared', [MessagesController::class, 'sharedPhotos'])->name('api.chatify.shared');
    Route::post('/deleteConversation', [MessagesController::class, 'deleteConversation'])->name('api.chatify.deleteConversation');
    Route::post('/deleteMessage', [MessagesController::class, 'deleteMessage'])->name('api.chatify.deleteMessage');
    Route::post('/updateSettings', [MessagesController::class, 'updateSettings'])->name('api.chatify.updateSettings');
    Route::post('/setActiveStatus', [MessagesController::class, 'setActiveStatus'])->name('api.chatify.setActiveStatus');
});
