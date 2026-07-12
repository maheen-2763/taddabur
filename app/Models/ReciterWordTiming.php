<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReciterWordTiming extends Model
{
    protected $fillable = [
        'reciter_id',
        'surah_number',
        'ayah_number',
        'word_start_index',
        'word_end_index',
        'start_ms',
        'end_ms',
    ];

    public function reciter(): BelongsTo
    {
        return $this->belongsTo(Recitation::class, 'reciter_id');
    }
}
