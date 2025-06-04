<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrackRestoredByAdmin extends Notification
{
    use Queueable;

    public $trackTitle;

    public function __construct(string $trackTitle)
    {
        $this->trackTitle = $trackTitle;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your track "' . $this->trackTitle . '" was restored by an admin.',
        ];
    }
}
