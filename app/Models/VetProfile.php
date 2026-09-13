<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VetProfile extends Model
{
    protected $fillable = [
        'user_id', 'qualification', 'experience_years', 'clinic_name',
        'clinic_address', 'bio', 'consultation_fee',
        'is_verified', 'verified_at', 'verified_by',
        'rejection_reason', 'rejected_at',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'consultation_fee' => 'decimal:2',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
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
