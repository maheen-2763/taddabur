<?php
// app/Models/AyahTranslation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AyahTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'ayah_id',
        'translation_id',
        'text',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function ayah(): BelongsTo
    {
        return $this->belongsTo(Ayah::class);
    }

    public function translation(): BelongsTo
    {
        return $this->belongsTo(Translation::class);
    }
}
