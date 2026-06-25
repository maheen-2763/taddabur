<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HadithCollection extends Model
{
    protected $fillable = ['name', 'arabic_name', 'slug', 'scholar', 'period', 'total_hadith'];
    public function chapters()
    {
        return $this->hasMany(HadithChapter::class);
    }
    public function hadiths()
    {
        return $this->hasMany(Hadith::class);
    }
}
