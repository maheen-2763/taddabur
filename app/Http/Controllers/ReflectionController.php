<?php

namespace App\Http\Controllers;

use App\Models\DailyContent;
use App\Services\QuranService;
use Illuminate\Support\Facades\Auth;

class ReflectionController extends Controller
{
    public function __construct(private QuranService $quranService) {}

    public function show(DailyContent $dailyContent)
    {
        if ($dailyContent->type === 'ayah') {
            $user = Auth::user();
            $translation = $this->quranService->resolveTranslation(
                $user?->preferred_translation ?? QuranService::DEFAULT_TRANSLATION,
                $user
            );
            $dailyContent->load([
                'ayah.surah',
                'ayah.translations' => fn($q) => $q->where('translation_id', $translation?->id),
            ]);
        } elseif ($dailyContent->type === 'hadith') {
            $dailyContent->load(['hadith.chapter', 'hadith.collection']);
        }

        return view('reflections.show', compact('dailyContent'));
    }
}
