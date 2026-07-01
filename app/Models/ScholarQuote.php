<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarQuote extends Model
{
    protected $fillable = ['scholar_id', 'quote_arabic', 'quote_english', 'source'];

    public function scholar(): BelongsTo
    {
        return $this->belongsTo(Scholar::class);
    }
}
