<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialization extends Model
{
    protected $fillable = ['name', 'description', 'status'];

    public function vets(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'vet_specializations', 'specialization_id', 'vet_id');
    }
}
