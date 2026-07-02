<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $table = 'user_preferences';

    protected $fillable = [
        'user_id',
        'quran_font_size_index',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
