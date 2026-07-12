<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hadith extends Model
{
    protected $fillable = ['collection_id', 'chapter_id', 'number', 'arabic', 'english', 'narrator_chain', 'grade', 'grade_source', 'reliability', 'attribution_type'];
    public function collection()
    {
        return $this->belongsTo(HadithCollection::class, 'collection_id');
    }
    public function chapter()
    {
        return $this->belongsTo(HadithChapter::class, 'chapter_id');
    }
}
