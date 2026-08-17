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

    protected $appends = ['has_verified_timing']; // fixed — was 'timing_accuracy', didn't match the accessor below

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

    // Is this reciter available to the given user (or a guest, if null)?
    // Free reciters (currently just Mishary) — open to everyone.
    // Everything else needs a paid plan (Basic or Premium both qualify).
    public function isAccessibleBy(?User $user): bool
    {
        return true; // Sab reciters sabke liye accessible hain
    }

    const REAL_TIMING_RECITERS = [
        'mishary-rashid',
        'al-shuraim',
        'al-minshawi',
        'ar-rifai',
        'abdul-basit',
        'al-husary',
    ];

    public function getHasVerifiedTimingAttribute(): bool
    {
        return in_array($this->slug, self::REAL_TIMING_RECITERS);
    }


    // app/Models/Recitation.php
    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');
    }
}
