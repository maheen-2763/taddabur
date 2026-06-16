<?php
// app/Models/Tafsir.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tafsir extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'scholar',
        'language_code',
        'language_name',
        'slug',
        'source',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function ayahTafsirs(): HasMany
    {
        return $this->hasMany(AyahTafsir::class);
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeInLanguage($query, string $languageCode)
    {
        return $query->where('language_code', $languageCode);
    }
}
