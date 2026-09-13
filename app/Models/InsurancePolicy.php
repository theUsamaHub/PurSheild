<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsurancePolicy extends Model
{
    protected $fillable = [
        'pet_id', 'provider_name', 'policy_number', 'start_date',
        'expiry_date', 'policy_document', 'claim_document', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
