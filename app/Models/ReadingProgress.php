<?php
// app/Models/ReadingProgress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\UserReadAyah;

class ReadingProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'last_ayah_id',
        'quran_ayahs_read',
        'story_id',
        'last_chapter_id',
        'reading_streak_days',
        'last_read_at',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastAyah(): BelongsTo
    {
        return $this->belongsTo(Ayah::class, 'last_ayah_id');
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function lastChapter(): BelongsTo
    {
        return $this->belongsTo(StoryChapter::class, 'last_chapter_id');
    }

    // -------------------------------------------------------
    // METHODS
    // -------------------------------------------------------

    // Update the reading streak
    // Call this every time a user reads something
    public function updateStreak(): void
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        if ($this->last_read_at?->toDateString() === $today) {
            // Already read today — no change needed
            return;
        }

        if ($this->last_read_at?->toDateString() === $yesterday) {
            // Read yesterday — extend streak
            $this->increment('reading_streak_days');
        } else {
            // Missed a day — reset streak to 1
            $this->update(['reading_streak_days' => 1]);
        }

        $this->update(['last_read_at' => now()]);
    }

    // Quran completion percentage
    public function getQuranProgressPercentageAttribute(): float
    {
        $ayahsRead = UserReadAyah::where(
            'user_id',
            $this->user_id
        )->count();

        return round(($ayahsRead / 6236) * 100, 1);
    }
}
