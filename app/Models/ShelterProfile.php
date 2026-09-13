<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShelterProfile extends Model
{
    protected $fillable = [
        'user_id', 'shelter_name', 'description', 'address', 'city',
        'contact_number', 'website', 'is_verified', 'verified_at',
        'verified_by', 'capacity', 'latitude', 'longitude',
        'rejection_reason', 'rejected_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
        'capacity' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
