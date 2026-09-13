<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    protected $fillable = [
        'pet_id', 'owner_id', 'vet_id', 'appointment_date',
        'appointment_time', 'reason', 'status', 'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function treatment(): HasOne
    {
        return $this->hasOne(Treatment::class);
    }
}
