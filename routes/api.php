<?php

use Illuminate\Support\Facades\Route;
use App\Models\Surah;
use App\Http\Controllers\WordTimingController;

// Used by admin daily content form
Route::get('/surahs/{surah}/ayahs', function (Surah $surah) {
    return $surah->ayahs()
        ->select('id', 'number', 'text_arabic')
        ->orderBy('number')
        ->get();
});

// routes/web.php or api.php
