<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarTeaching extends Model
{
    protected $fillable = ['scholar_id', 'title', 'content', 'order_index'];

    public function scholar(): BelongsTo
    {
        return $this->belongsTo(Scholar::class);
    }
}
