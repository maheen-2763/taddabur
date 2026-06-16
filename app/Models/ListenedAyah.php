<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListenedAyah extends Model
{
    protected $fillable = [
        'user_id',
        'surah_id',
        'ayah_id'
    ];
}
