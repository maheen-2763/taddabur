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
}
