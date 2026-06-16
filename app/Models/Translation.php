<?php
// app/Models/Translation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'author',
        'language_code',
        'language_name',
        'slug',
        'source',
        'is_free',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_free'   => 'boolean',
        'is_active' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    // A translation collection has many individual ayah translations
    public function ayahTranslations(): HasMany
    {
        return $this->hasMany(AyahTranslation::class);
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopeForLanguage($query, string $languageCode)
    {
        return $query->where('language_code', $languageCode);
    }
}
