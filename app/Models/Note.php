<?php
// app/Models/Note.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'ayah_id',
        'story_id',
        'hadith_id',
        'title',
        'content',
        'color',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Note might be linked to an ayah
    public function ayah(): BelongsTo
    {
        return $this->belongsTo(Ayah::class);
    }

    // Note might be linked to a story
    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function hadith(): BelongsTo
    {
        return $this->belongsTo(Hadith::class);
    }
    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    // Only this user's notes
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Notes attached to quran (have ayah_id)
    public function scopeQuranNotes($query)
    {
        return $query->whereNotNull('ayah_id');
    }

    // Notes attached to stories (have story_id)
    public function scopeStoryNotes($query)
    {
        return $query->whereNotNull('story_id');
    }

    // Notes attached to Hadiths (have hadith_id)
    public function scopeHadithNotes($query)
    {
        return $query->whereNotNull('hadith_id');
    }


    public function getReferenceIconAttribute(): string
    {
        return match (true) {
            $this->ayah_id !== null => 'bi-book',
            $this->hadith_id !== null => 'bi-collection',
            $this->story_id !== null => 'bi-journal-bookmark',
            default => 'bi-sticky',
        };
    }

    public function getReferenceLabelAttribute(): string
    {
        return match (true) {
            $this->ayah_id !== null && $this->ayah =>
            $this->ayah->surah->name_transliteration . ' ' . $this->ayah->surah->number . ':' . $this->ayah->number,
            $this->hadith_id !== null && $this->hadith =>
            $this->hadith->collection->name . ' #' . $this->hadith->number,
            $this->story_id !== null && $this->story =>
            $this->story->title,
            default => 'General Note',
        };
    }

    public function getReferenceUrlAttribute(): ?string
    {
        return match (true) {
            $this->ayah_id !== null && $this->ayah =>
            route('quran.show', $this->ayah->surah->number) . '#ayah-' . $this->ayah->number,
            $this->hadith_id !== null && $this->hadith =>
            route('hadith.show', [$this->hadith->collection->slug, $this->hadith->chapter->number]) . '?highlight=' . $this->hadith->id,
            default => null,
        };
    }
}
