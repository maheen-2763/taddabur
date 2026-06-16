<?php
// app/Models/AyahTafsir.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AyahTafsir extends Model
{
    use HasFactory;

    protected $fillable = [
        'ayah_id',
        'tafsir_id',
        'text',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function ayah(): BelongsTo
    {
        return $this->belongsTo(Ayah::class);
    }

    public function tafsir(): BelongsTo
    {
        return $this->belongsTo(Tafsir::class);
    }

    // -------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------

    // Preview of tafsir — first 200 characters
    // Useful for listing pages without loading full text
    public function getPreviewAttribute(): string
    {
        return str($this->text)->limit(200)->toString();
    }
}
