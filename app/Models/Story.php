<?php
// app/Models/Story.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Story extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prophet_id',
        'title',
        'slug',
        'category',
        'subject',
        'summary',
        'cover_image',
        'difficulty',
        'is_free',
        'is_published',
        'sort_order',
        'read_time_minutes',
        'quran_references',
        'tags',
        'word_count',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'quran_references' => 'array',
        'tags' => 'array',
    ];



    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    // A story optionally belongs to a prophet
    public function prophet(): BelongsTo
    {
        return $this->belongsTo(Prophet::class);
    }

    // A story has many chapters
    public function chapters(): HasMany
    {
        return $this->hasMany(StoryChapter::class, 'story_id')->orderBy('order');
    }

    // A story can be bookmarked (polymorphic)
    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    // Only published stories
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Only free stories (for free plan users)
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    // Filter by category: Story::ofCategory('prophet')->get()
    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // Filter by difficulty
    public function scopeForBeginners($query)
    {
        return $query->where('difficulty', 'beginner');
    }

    // -------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------

    public function getCoverImageUrlAttribute(): string
    {
        return $this->cover_image
            ? Storage::url($this->cover_image)
            : asset('images/default-story.png');
    }

    // Total word count across all chapters
    public function getWordCountAttribute(): int
    {
        return $this->chapters()->sum('word_count');
    }

    public function getChapterCountAttribute(): int
    {
        return $this->chapters()->count();
    }

    public function getFormattedReadTimeAttribute(): string
    {
        return $this->read_time_minutes . ' min read';
    }


    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function nextStory()
    {
        return static::where(
            'sort_order',
            '>',
            $this->sort_order
        )
            ->orderBy('sort_order')
            ->first();
    }

    public function previousStory()
    {
        return static::where(
            'sort_order',
            '<',
            $this->sort_order
        )
            ->orderByDesc('sort_order')
            ->first();
    }


    // -------------------------------------------------------
    // scopes for category filtering (prophet, companion, general) - for convenience in controllers and views
    // -------------------------------------------------------

    public function scopeProphets($query)
    {
        return $query->where('category', 'prophet');
    }

    public function scopeCompanions($query)
    {
        return $query->where('category', 'companion');
    }

    public function scopeGeneral($query)
    {
        return $query->where('category', 'general');
    }
}
