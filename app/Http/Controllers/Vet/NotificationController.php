<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\FurshieldNotification;
use App\Support\VetNotifications;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['status' => ['nullable', 'in:all,unread,read']]);
        $query = VetNotifications::query($request->user());
        if (in_array($filters['status'] ?? 'all', ['unread', 'read'])) {
            $query->where('is_read', $filters['status'] === 'read');
        }
        $notifications = $query->orderByDesc('created_at')->orderBy('source')->orderByDesc('id')
            ->paginate(15)->withQueryString()->through([VetNotifications::class, 'present']);

        return view('vet.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, string $source, string $notification): RedirectResponse
    {
        if ($source === 'practice') {
            FurshieldNotification::where('user_id', $request->user()->id)->findOrFail($notification)->markAsRead();
        } else {
            $request->user()->notifications()->findOrFail($notification)->markAsRead();
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        VetNotifications::markAllRead($request->user());

        return back()->with('success', 'All notifications marked as read.');
    }
}
