<?php

namespace App\Http\Controllers;

use App\Models\AllahName;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Ayah;
use App\Models\Hadith;
use App\Models\Story;
use App\Models\StoryChapter;
use App\Models\Prophet;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $allahNames = AllahName::inRandomOrder()
            ->take(20)
            ->get();

        $hadithCount = (int) str_replace(',', '', config('content_sources.stats.1.number'));

        $heroAyahs = Ayah::whereHas('surah', fn($q) => $q->where('number', 96))
            ->whereIn('number', [1, 5])
            ->with('translations')
            ->orderBy('number')
            ->get();

        $reflectionAyah = Ayah::whereHas('surah', fn($q) => $q->where('number', 38))
            ->where('number', 29)
            ->with('translations')
            ->first();

        $storiesStats = Cache::remember('stories_stats', now()->addHours(6), function () {
            return [
                'total_prophets'  => Prophet::count(),
                'total_stories'   => Story::where('is_published', true)->count(),
                'total_chapters'  => StoryChapter::count(),
                'quran_references' => DB::table('story_chapters')
                    ->selectRaw('SUM(json_array_length(quran_references)) as total')
                    ->whereNotNull('quran_references')
                    ->value('total') ?? 0,
                'hadith_references' => DB::table('story_chapters')
                    ->selectRaw('SUM(json_array_length(hadith_references)) as total')
                    ->whereNotNull('hadith_references')
                    ->value('total') ?? 0,
            ];
        });

        return view('welcome', compact('allahNames', 'hadithCount', 'reflectionAyah', 'heroAyahs', 'storiesStats'));
    }
}
