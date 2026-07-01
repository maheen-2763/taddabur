<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarStudent extends Model
{
    protected $fillable = ['scholar_id', 'name', 'arabic_name', 'description'];

    public function scholar(): BelongsTo
    {
        return $this->belongsTo(Scholar::class);
    }
}
