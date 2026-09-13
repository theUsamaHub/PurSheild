<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    protected $fillable = [
        'owner_id', 'species_id', 'breed_id', 'name', 'gender',
        'date_of_birth', 'weight', 'color', 'description', 'profile_image',
        'is_neutered', 'microchip_number', 'adoption_fee',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'weight' => 'decimal:2',
        'is_neutered' => 'boolean',
        'adoption_fee' => 'decimal:2',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
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
        return $this->hasMany(PetImage::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class);
    }

    public function medicalDocuments(): HasMany
    {
        return $this->hasMany(MedicalDocument::class);
    }

    public function insurancePolicies(): HasMany
    {
        return $this->hasMany(InsurancePolicy::class);
    }
}
