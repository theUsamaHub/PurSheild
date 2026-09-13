<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Treatment extends Model
{
    protected $fillable = [
        'appointment_id', 'pet_id', 'vet_id', 'symptoms',
        'diagnosis', 'treatment', 'follow_up_date', 'notes',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
