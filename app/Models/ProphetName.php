<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProphetName extends Model
{
    protected $fillable = [
        'name_ar',
        'name_transliteration',
        'meaning',
        'tier',
        'source_type',
        'source_reference',
        'hadith_id',
        'ayah_id',
        'all_references',
        'sort_order',
    ];

    protected $casts = [
        'all_references' => 'array',
    ];

    public function hadith()
    {
        return $this->belongsTo(Hadith::class);
    }


    public function ayah()
    {
        return $this->belongsTo(Ayah::class);
    }
}
