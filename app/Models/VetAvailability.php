<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VetAvailability extends Model
{
    public $timestamps = false;

    protected $fillable = ['vet_id', 'day_of_week', 'start_time', 'end_time', 'is_available'];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }
}
