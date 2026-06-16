<?php

use Illuminate\Support\Facades\Route;
use App\Models\Surah;

// Used by admin daily content form
Route::get('/surahs/{surah}/ayahs', function (Surah $surah) {
    return $surah->ayahs()
        ->select('id', 'number', 'text_arabic')
        ->orderBy('number')
        ->get();
});
