<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'owner_id', 'order_number', 'total_amount', 'status',
        'shipping_address', 'notes', 'status_history', 'order_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'status_history' => 'array',
        'order_date' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
