<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccination extends Model
{
    protected $fillable = [
        'pet_id', 'vaccine_name', 'vaccination_date',
        'next_due_date', 'batch_number', 'notes',
    ];

    protected $casts = [
        'vaccination_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
