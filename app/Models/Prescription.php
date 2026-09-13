<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    protected $fillable = [
        'treatment_id', 'medicine_name', 'dosage',
        'frequency', 'duration', 'instructions',
    ];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }
}
