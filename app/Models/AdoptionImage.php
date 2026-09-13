<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdoptionImage extends Model
{
    public $timestamps = false;

    protected $fillable = ['listing_id', 'image_path', 'caption'];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(AdoptionListing::class, 'listing_id');
    }
}
