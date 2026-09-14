<?php

namespace App\Support;

use App\Models\FurshieldNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class VetNotifications
{
    public static function query(User $user): Builder
    {
        $practice = DB::table('furshield_notifications')->where('user_id', $user->id)
            ->selectRaw("id, 'practice' as source, title, message, type, link, is_read, created_at, NULL as data");
        $legacy = DB::table('notifications')
            ->where('notifiable_id', $user->id)->where('notifiable_type', $user->getMorphClass())
            ->selectRaw("id, 'system' as source, NULL as title, NULL as message, NULL as type, NULL as link, CASE WHEN read_at IS NULL THEN 0 ELSE 1 END as is_read, created_at, data");

        return DB::query()->fromSub($practice->unionAll($legacy), 'vet_notifications');
    }

    public static function present(object $notification): object
    {
        $data = json_decode($notification->data ?? '{}', true) ?: [];
        $notification->title = $notification->title ?? $data['title'] ?? 'Notification';
        $notification->message = $notification->message ?? $data['message'] ?? '';
        $notification->type = $notification->type ?? $data['type'] ?? 'default';
        $link = $notification->link ?? $data['link'] ?? $data['url'] ?? '';
        // Only allow local navigation from notification data.
        $notification->link = is_string($link) && str_starts_with($link, '/')
            && ! str_starts_with($link, '//') && ! str_contains($link, '\\') ? $link : null;
        $notification->created_at = Carbon::parse($notification->created_at);

        return $notification;
    }

    public static function unreadCount(User $user): int
    {
        return self::query($user)->where('is_read', false)->count();
    }

    public static function markAllRead(User $user): void
    {
        DB::transaction(function () use ($user) {
            FurshieldNotification::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);
            $user->unreadNotifications()->update(['read_at' => now()]);
        });
    }
}
