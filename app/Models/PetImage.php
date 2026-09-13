<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetImage extends Model
{
    protected $fillable = ['pet_id', 'image_path', 'caption', 'is_primary', 'sort_order'];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
