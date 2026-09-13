<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalDocument extends Model
{
    protected $fillable = [
        'pet_id', 'health_record_id', 'document_type',
        'file_name', 'file_path', 'description', 'mime_type',
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function healthRecord(): BelongsTo
    {
        return $this->belongsTo(HealthRecord::class);
    }
}
