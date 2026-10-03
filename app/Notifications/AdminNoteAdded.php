<?php

namespace App\Notifications;

use App\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNoteAdded extends Notification
{
    use Queueable;

    public function __construct(private readonly Assessment $assessment)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Store a translation key rather than translated text, so the
     * notification reads correctly in whichever language the SME owner
     * is browsing in at the time they view it, not when it was created.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_key' => 'Administrator left feedback on your assessment.',
            'url' => route('assessment.result', $this->assessment),
        ];
    }
}
