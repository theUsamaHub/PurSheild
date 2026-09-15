<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'category', 'content_type', 'content',
        'media_url', 'thumbnail', 'status',
    ];
}
