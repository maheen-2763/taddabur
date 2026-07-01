<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarWork extends Model
{
    protected $fillable = ['scholar_id', 'title', 'arabic_title', 'description'];

    public function scholar(): BelongsTo
    {
        return $this->belongsTo(Scholar::class);
    }
}
