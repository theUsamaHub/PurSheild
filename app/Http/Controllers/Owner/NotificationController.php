<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\FurshieldNotification;
use App\Support\VetNotifications;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(Request $request, string $source, string $notification)
    {
        if ($source === 'practice') {
            FurshieldNotification::where('user_id', $request->user()->id)->findOrFail($notification)->markAsRead();
        } else {
            $request->user()->notifications()->findOrFail($notification)->markAsRead();
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function readAll(Request $request)
    {
        VetNotifications::markAllRead($request->user());

        return back()->with('success', 'All notifications marked as read.');
    }
}
