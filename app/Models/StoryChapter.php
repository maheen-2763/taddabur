<?php
// app/Models/StoryChapter.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class StoryChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'title',
        'content',
        'order',
        'quran_references',
        'hadith_references',
        'image',
        'slug',
    ];

    protected $casts = [
        'quran_references'  => 'array',
        'hadith_references' => 'array',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    // Chapters can be bookmarked (polymorphic)
    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    // -------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------



    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/default-story-chapter.png');
    }
    // Estimated reading time for this chapter
    public function getReadTimeAttribute(): string
    {
        $words = str_word_count(
            strip_tags(
                html_entity_decode($this->content)
            )
        );
        $minutes = ceil($words / 200); // average reading speed

        return $minutes . ' min read';
    }

    // Next chapter in this story
    public function getNextChapterAttribute(): ?StoryChapter
    {
        return StoryChapter::where('story_id', $this->story_id)
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    // Previous chapter in this story
    public function getPreviousChapterAttribute(): ?StoryChapter
    {
        return StoryChapter::where('story_id', $this->story_id)
            ->where('order', '<', $this->order)
            ->orderByDesc('order')
            ->first();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }



    protected static function booted()
    {
        static::creating(function ($chapter) {
            if (!$chapter->slug) {
                $chapter->slug = Str::slug($chapter->title);
            }
        });

        static::updating(function ($chapter) {
            if (!$chapter->slug) {
                $chapter->slug = Str::slug($chapter->title);
            }
        });
    }
}
