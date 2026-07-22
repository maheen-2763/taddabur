<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HadithCollection extends Model
{
    protected $fillable = ['name', 'arabic_name', 'slug', 'scholar', 'period', 'total_hadith'];
    public function chapters()
    {
        return $this->hasMany(HadithChapter::class, 'collection_id');
    }
    public function hadiths()
    {
        return $this->hasMany(Hadith::class, 'collection_id'); // 👈 explicitly batao
    }

    // ✅ Accessor — agar total_hadith stale/null ho, live count se fallback
    public function getDisplayCountAttribute(): int
    {
        return $this->hadiths_count ?? $this->total_hadith ?? 0;
    }
}
