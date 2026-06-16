<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prophet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_arabic',
        'name_english',
        'name_transliteration',
        'slug',
        'title',
        'title_arabic',
        'title_transliteration',
        'order',
        'summary',
        'period',
        'mentioned_in_quran',
        'quran_mentions_count',
        'cover_image',
        'timeline',
    ];

    protected $casts = [
        'mentioned_in_quran' => 'boolean',
        'timeline' => 'array',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class)->orderBy('sort_order');
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    // Prophet::ordered()->get()
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // -------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------

    // Returns (SAW) for Muhammad, (AS) for all others
    public function getHonorificAttribute(): string
    {
        return $this->slug === 'muhammad'
            ? 'ﷺ'
            : 'عليه السلام';
    }

    // Full display name with correct honorific
    // $prophet->display_name → "Ibrahim (AS)" or "Muhammad (SAW)"
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name_transliteration} {$this->honorific}";
    }

    public function publishedStories(): HasMany
    {
        return $this->hasMany(Story::class)->where('is_published', true);
    }

    public function getCoverImageUrlAttribute(): string
    {
        return $this->cover_image
            ? Storage::url($this->cover_image)
            : asset('images/default-prophet.png');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function chapters()
    {
        return $this->hasManyThrough(
            StoryChapter::class,
            Story::class
        );
    }
}
