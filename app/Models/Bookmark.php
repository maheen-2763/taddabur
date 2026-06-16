<?php
// app/Models/Bookmark.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bookmarkable_type',
        'bookmarkable_id',
        'label',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    // A bookmark belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // The polymorphic relationship — returns either Ayah or StoryChapter
    // morphTo() tells Laravel: "look at bookmarkable_type to know which model to load"
    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    // Only bookmarks that are ayahs
    public function scopeAyahs($query)
    {
        return $query->where('bookmarkable_type', Ayah::class);
    }

    // Only bookmarks that are story chapters
    public function scopeStoryChapters($query)
    {
        return $query->where('bookmarkable_type', StoryChapter::class);
    }
}
