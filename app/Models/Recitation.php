<?php
// app/Models/Recitation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'style',
        'audio_url_pattern',
        'photo',
        'is_free',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_free'   => 'boolean',
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // -------------------------------------------------------
    // METHODS
    // -------------------------------------------------------

    // Generate the audio URL for a specific ayah
    // $recitation->audioUrlFor($surah, $ayah)
    public function audioUrlFor(Surah $surah, Ayah $ayah): string
    {

        $surahPadded = str_pad($surah->number, 3, '0', STR_PAD_LEFT);
        $ayahPadded  = str_pad($ayah->number,  3, '0', STR_PAD_LEFT);

        return str_replace(
            ['{surah_padded}', '{ayah_padded}'],
            [$surahPadded, $ayahPadded],
            $this->audio_url_pattern
        );
    }
}
