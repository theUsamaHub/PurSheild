<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FurshieldNotification extends Model
{
    protected $fillable = [
        'user_id', 'title', 'message', 'type', 'link', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }
}
