<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllahName extends Model
{
    protected $fillable = [
        'position',
        'name_ar',
        'transliteration',
        'english_name',
        'meaning',
        'reference',
        'slug',
        'description',

    ];
}
