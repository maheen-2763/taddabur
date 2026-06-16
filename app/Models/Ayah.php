<?php
// app/Models/Ayah.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ayah extends Model
{
    use HasFactory;

    protected $fillable = [
        'surah_id',
        'number',
        'number_in_quran',
        'text_arabic',
        'text_arabic_simple',
        'page',
        'juz',
        'hizb',
        'ruku',
        'sajda',
    ];

    protected $casts = [
        'sajda' => 'boolean',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    // An ayah belongs to a surah
    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }

    // An ayah has many translations (one per language/translator)
    public function translations(): HasMany
    {
        return $this->hasMany(AyahTranslation::class);
    }

    // An ayah has many tafsirs (one per scholar)
    public function tafsirs(): HasMany
    {
        return $this->hasMany(AyahTafsir::class);
    }

    // An ayah can be bookmarked by many users (polymorphic)
    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }


    // An ayah can have many user notes (polymorphic)
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    // Ayah::inJuz(30)->get() — get all ayahs in Juz Amma
    public function scopeInJuz($query, int $juz)
    {
        return $query->where('juz', $juz);
    }

    // Ayah::onPage(1)->get()
    public function scopeOnPage($query, int $page)
    {
        return $query->where('page', $page);
    }

    // Ayah::sajdaVerses()->get() — get all prostration verses
    public function scopeSajdaVerses($query)
    {
        return $query->where('sajda', true);
    }

    // -------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------

    // Get translation in a specific language
    // $ayah->getTranslation('en')
    public function getTranslation(string $languageCode): ?string
    {
        return $this->translations
            ->whereHas('translation', fn($q) => $q->where('language_code', $languageCode))
            ->first()
            ?->text;
    }

    // Reference string: "Al-Fatihah 1:1"
    public function getReferenceAttribute(): string
    {
        return "{$this->surah->name_transliteration} {$this->surah->number}:{$this->number}";
    }

    // Padded ayah number for audio URLs
    public function getPaddedNumberAttribute(): string
    {
        return str_pad($this->number, 3, '0', STR_PAD_LEFT);
    }
}
