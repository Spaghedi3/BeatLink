<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TrackRemovedByAdmin extends Notification
{
    use Queueable;

    public $trackTitle;
    public $reason;

    public function __construct(string $trackTitle, ?string $reason)
    {
        $this->trackTitle = $trackTitle;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your track "' . $this->trackTitle . '" was removed by an admin.',
            'reason' => $this->reason ?? 'No specific reason provided.',
        ];
    }
}
