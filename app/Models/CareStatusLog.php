<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareStatusLog extends Model
{
    protected $fillable = [
        'shelter_id', 'listing_id', 'animal_name', 'type', 'notes', 'log_date', 'log_time', 'staff_name', 'status', 'due_date',
    ];

    protected $casts = [
        'log_date' => 'date', 'due_date' => 'date',
    ];

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shelter_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(AdoptionListing::class, 'listing_id');
    }
}
