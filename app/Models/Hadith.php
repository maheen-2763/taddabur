<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hadith extends Model
{
    protected $fillable = ['collection_id', 'chapter_id', 'number', 'arabic', 'english', 'narrator_chain', 'grade', 'grade_source'];
    public function collection()
    {
        return $this->belongsTo(HadithCollection::class);
    }
    public function chapter()
    {
        return $this->belongsTo(HadithChapter::class);
    }
}
