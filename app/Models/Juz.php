<?php

// app/Models/Juz.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juz extends Model
{
    protected $fillable = [
        'number',
        'verse_mapping',
        'name_arabic',
        'name_english',
        'start_surah_id',
        'start_ayah',
        'end_surah_id',
        'end_ayah',
        'slug',
    ];

    protected $casts = [
        'verse_mapping' => 'array',
    ];
}
