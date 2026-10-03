<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark a single notification read, then send the user to whatever
     * it was about.
     */
    public function read(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('dashboard'));
    }

    /**
     * Mark every unread notification as read, staying on the same page.
     */
    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
