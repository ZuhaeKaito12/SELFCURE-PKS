<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminMessage extends Notification
{
    use Queueable;

    public function __construct(private readonly string $message)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message_key' => $this->message,
            'is_raw' => true,
            'url' => route('dashboard'),
        ];
    }
}
