<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    }

    public function readAndRedirect(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (!isset($notification->data['actor_id'])) {
            return back();
        }

        $actor = User::find($notification->data['actor_id']);

        if (!$actor) {
            return back();
        }

        return redirect()->route('user.tracks', $actor->username);
    }
}
