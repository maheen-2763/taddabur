<?php

namespace App\Http\Controllers;

use App\Models\Recitation;
use App\Models\ReciterWordTiming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WordTimingController extends Controller
{
    public function show($surahNumber, $ayahNumber, $reciterSlug)
    {
        $reciter = Recitation::where('slug', $reciterSlug)->first();

        if (!$reciter) {
            return response()->json(['timings' => null, 'accuracy' => 'unknown']);
        }

        if (!$reciter->has_verified_timing) {
            return response()->json(['timings' => null, 'accuracy' => 'weighted_estimate']);
        }

        $timings = ReciterWordTiming::where('reciter_id', $reciter->id)
            ->where('surah_number', $surahNumber)
            ->where('ayah_number', $ayahNumber)
            ->orderBy('word_index')
            ->get(['word_index', 'start_ms', 'end_ms']);

        return response()->json([
            'timings' => $timings,
            'accuracy' => 'verified_segments',
        ]);
    }
}
