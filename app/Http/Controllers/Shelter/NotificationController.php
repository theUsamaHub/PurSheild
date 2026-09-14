<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\FurshieldNotification;
use App\Support\VetNotifications;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $f = $request->validate(['status' => ['nullable', 'in:all,read,unread']]);
        $q = VetNotifications::query($request->user());
        if (in_array($f['status'] ?? 'all', ['read', 'unread'])) {
            $q->where('is_read', $f['status'] === 'read');
        }
        $notifications = $q->orderByDesc('created_at')->orderByDesc('id')->paginate(10)->withQueryString()->through([VetNotifications::class, 'present']);

        return view('shelter.notifications.index', compact('notifications'));
    }

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
