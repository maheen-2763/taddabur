<?php
// app/Models/Surah.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Surah extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'name_arabic',
        'name_transliteration',
        'name_english',
        'revelation_type',
        'ayah_count',
        'page_number',
        'description',
        'slug',
        'juz_start',
        'juz_end',

    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    // A surah has many ayahs
    public function ayahs(): HasMany
    {
        return $this->hasMany(Ayah::class)->orderBy('number');
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    // Filter by revelation type: Surah::meccan()->get()
    public function scopeMeccan($query)
    {
        return $query->where('revelation_type', 'meccan');
    }

    public function scopeMedinan($query)
    {
        return $query->where('revelation_type', 'medinan');
    }

    // -------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------

    // Get surah number padded to 3 digits: "001", "114"
    // Used for building audio URLs and API calls
    public function getPaddedNumberAttribute(): string
    {
        return str_pad($this->number, 3, '0', STR_PAD_LEFT);
    }

    // Full display name: "1. Al-Fatihah (The Opening)"
    public function getFullNameAttribute(): string
    {
        return "{$this->number}. {$this->name_transliteration} ({$this->name_english})";
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
