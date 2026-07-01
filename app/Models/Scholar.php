<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scholar extends Model
{
    protected $fillable = [
        'name',
        'arabic_name',
        'birth_ah',
        'death_ah',
        'madhab',
        'biography',
        'early_life',
        'trials',
        'slug',
    ];

    // Relations
    public function teachings(): HasMany
    {
        return $this->hasMany(ScholarTeaching::class)
            ->orderBy('order_index');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(ScholarQuote::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(ScholarStudent::class);
    }

    public function works(): HasMany
    {
        return $this->hasMany(ScholarWork::class);
    }

    // Accessors
    public function getPeriodAttribute(): string
    {
        return "{$this->birth_ah}–{$this->death_ah} AH";
    }

    // Scope
    public function scopeByMadhab($query, string $madhab)
    {
        return $query->where('madhab', $madhab);
    }
}
