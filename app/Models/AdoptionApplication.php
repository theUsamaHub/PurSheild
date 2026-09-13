<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdoptionApplication extends Model
{
    protected $fillable = [
        'listing_id', 'applicant_id', 'message', 'phone', 'address',
        'home_type', 'has_yard', 'living_situation', 'has_other_pets',
        'other_pets_details', 'has_children', 'children_ages', 'work_schedule',
        'pet_experience', 'why_adopt', 'status', 'shelter_response',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(AdoptionListing::class, 'listing_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }
}
