<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ReactionNotification extends Notification
{
    use Queueable;

    protected $actor;
    protected $reaction;
    protected $track;

    public function __construct($actor, string $reaction, $track)
    {
        $this->actor    = $actor;       // e.g. User instance who clicked
        $this->reaction = strtoupper($reaction); // “LOVE” or “HATE”
        $this->track    = $track;       // Track instance
    }

    public function via($notifiable)
    {
        return ['database']; // store in notifications table
    }

    public function toDatabase($notifiable)
    {
        return [
            'message'        => "{$this->actor->username} {$this->reaction} {$this->track->name}",
            'track_id'       => $this->track->id,
            'actor_id'       => $this->actor->id,
            'actor_username' => $this->actor->username,
            'reaction'       => $this->reaction,
        ];
    }
}
