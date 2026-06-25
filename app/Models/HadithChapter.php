<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HadithChapter extends Model
{
    protected $fillable = ['collection_id', 'number', 'title', 'arabic_title'];
    public function collection()
    {
        return $this->belongsTo(HadithCollection::class);
    }
    public function hadiths()
    {
        return $this->hasMany(Hadith::class);
    }
}
