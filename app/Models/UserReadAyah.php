<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReadAyah extends Model
{
    protected $fillable = [
        'user_id',
        'ayah_id',
    ];


    public function ayah(): BelongsTo
    {
        return $this->belongsTo(Ayah::class);
    }
}
