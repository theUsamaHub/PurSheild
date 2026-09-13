<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AdoptionListing extends Model
{
    protected $fillable = [
        'shelter_id', 'species_id', 'breed_id', 'pet_name', 'age',
        'gender', 'health_status', 'description', 'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shelter_id');
    }

    public function shelterProfile(): BelongsTo
    {
        return $this->belongsTo(ShelterProfile::class, 'shelter_id', 'user_id');
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(AdoptionImage::class, 'listing_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(AdoptionApplication::class, 'listing_id');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
