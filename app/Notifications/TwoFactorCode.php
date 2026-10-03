<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCode extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your SELFCURE PKS verification code'))
            ->line(__('Your verification code is:'))
            ->line($this->code)
            ->line(__('This code will expire in 10 minutes. If you did not try to log in, you can ignore this email.'));
    }
}
