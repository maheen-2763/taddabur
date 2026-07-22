<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hadith extends Model
{
    protected $fillable = ['collection_id', 'chapter_id', 'number', 'arabic', 'english', 'narrator_chain', 'grade', 'grade_source', 'needs_review', 'reliability', 'attribution_type'];

    protected static function booted(): void
    {
        static::saving(function (Hadith $hadith) {
            if ($hadith->isDirty('grade')) {
                if ($hadith->grade) {
                    $mapped = config('hadith_grade_map')[$hadith->grade] ?? null;
                    $hadith->reliability = $mapped['reliability'] ?? null;
                    $hadith->attribution_type = $mapped['attribution'] ?? null;
                } else {
                    // grade null ho gaya, to derived columns bhi null hone chahiye
                    $hadith->reliability = null;
                    $hadith->attribution_type = null;
                }
            }
        });
    }

    public function collection()
    {
        return $this->belongsTo(HadithCollection::class, 'collection_id');
    }

    public function chapter()
    {
        return $this->belongsTo(HadithChapter::class, 'chapter_id');
    }

    // Hadith.php — agar reverse relation kahin chahiye ho
    public function readBy()
    {
        return $this->belongsToMany(User::class, 'hadith_reads')->withPivot('read_at');
    }
}
