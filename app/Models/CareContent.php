<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CareContent extends Model
{
    protected $fillable = [
        'title', 'category', 'content_type', 'content',
        'media_url', 'thumbnail', 'status',
    ];
}
