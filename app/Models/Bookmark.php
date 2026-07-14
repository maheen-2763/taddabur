<?php
// app/Models/Bookmark.php

namespace App\Models;

use App\Models\Ayah;
use App\Models\StoryChapter;
use App\Models\Hadith;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

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
    // app/Models/Bookmark.php

    public function bookmarkable()
    {
        return $this->morphTo();
    }



    public function getDisplayTitleAttribute(): string
    {
        return match ($this->bookmarkable_type) {
            Ayah::class         => "Surah {$this->bookmarkable?->surah?->name} — Ayah {$this->bookmarkable?->number}",
            StoryChapter::class => $this->bookmarkable?->title ?? 'Story Chapter',
            Hadith::class       => $this->bookmarkable?->chapter?->title ?? 'Hadith',
            default             => $this->label ?? 'Bookmark',
        };
    }

    public function getDisplayPreviewAttribute(): string
    {
        return match ($this->bookmarkable_type) {
            Ayah::class => Str::limit(
                $this->bookmarkable?->translations
                    ?->first(fn($t) => $t->translation?->slug === 'sahih-international')
                    ?->text ?? '',
                80
            ),
            StoryChapter::class => Str::limit(strip_tags($this->bookmarkable?->content ?? ''), 80),
            Hadith::class        => Str::limit($this->bookmarkable?->english ?? '', 80),
            default              => '',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->bookmarkable_type) {
            Ayah::class         => 'bi-book',
            StoryChapter::class => 'bi-journal-text',
            Hadith::class       => 'bi-collection',
            default             => 'bi-bookmark',
        };
    }

    public function getGradeAttribute(): ?string
    {
        return $this->bookmarkable_type === Hadith::class
            ? $this->bookmarkable?->grade
            : null;
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
