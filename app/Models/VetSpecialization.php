<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VetSpecialization extends Model
{
    public $timestamps = false;

    protected $fillable = ['vet_id', 'specialization_id'];

    public function vet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
}
