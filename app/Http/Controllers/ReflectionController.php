<?php

namespace App\Http\Controllers;

use App\Models\DailyContent;

class ReflectionController extends Controller
{
    public function show(DailyContent $dailyContent)
    {
        $dailyContent->load([
            'ayah.surah',
            'ayah.translations.translation',
        ]);

        return view(
            'reflections.show',
            compact('dailyContent')
        );
    }
}
