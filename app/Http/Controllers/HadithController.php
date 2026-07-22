<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\HadithChapter;
use App\Models\HadithCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HadithController extends Controller
{
    const PER_PAGE = 20;

    // ✅ Page 1: Saari Collections (Bukhari, Muslim, etc.)
    public function index()
    {
        $collections = HadithCollection::withCount('hadiths')->get();

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user) {
            // ✅ Single query — sab collections ka read-count ek saath (N+1 nahi)
            $readCounts = DB::table('hadith_reads')
                ->join('hadiths', 'hadiths.id', '=', 'hadith_reads.hadith_id')
                ->where('hadith_reads.user_id', $user->id)
                ->selectRaw('hadiths.collection_id, COUNT(*) as read_count')
                ->groupBy('hadiths.collection_id')
                ->pluck('read_count', 'collection_id');

            $collections->each(function ($c) use ($readCounts) {
                $read = $readCounts[$c->id] ?? 0;
                $c->read_count = $read;
                $c->progress_percent = $c->hadiths_count > 0
                    ? min(100, round(($read / $c->hadiths_count) * 100))
                    : 0;
            });
        }

        return view('hadith.index', compact('collections'));
    }

    public function chapters(HadithCollection $collection)
    {
        $chapters = HadithChapter::where('collection_id', $collection->id)
            ->withCount('hadiths')
            ->orderBy('number')
            ->get();

        $resumeHadith = null;
        $hasReadAnything = false;

        if ($user = Auth::user()) {
            // Step 1: last-read hadith is collection ke andar
            $lastRead = DB::table('hadith_reads')
                ->join('hadiths', 'hadiths.id', '=', 'hadith_reads.hadith_id')
                ->where('hadith_reads.user_id', $user->id)
                ->where('hadiths.collection_id', $collection->id)
                ->orderByDesc('hadith_reads.read_at')
                ->select('hadiths.chapter_id', 'hadiths.number')
                ->first();

            $hasReadAnything = (bool) $lastRead;

            if ($lastRead) {
                // Step 2: same chapter mein agla hadith dhoondo
                $resumeHadith = Hadith::where('chapter_id', $lastRead->chapter_id)
                    ->where('number', '>', $lastRead->number)
                    ->orderBy('number')
                    ->first();

                // Step 3: agar chapter khatam ho gaya, agle chapter ka pehla hadith
                if (!$resumeHadith) {
                    $currentChapter = HadithChapter::find($lastRead->chapter_id);

                    $nextChapter = HadithChapter::where('collection_id', $collection->id)
                        ->where('number', '>', $currentChapter->number)
                        ->orderBy('number')
                        ->first();

                    if ($nextChapter) {
                        $resumeHadith = Hadith::where('chapter_id', $nextChapter->id)
                            ->orderBy('number')
                            ->first();
                    }
                    // agar $nextChapter bhi nahi mila = poori collection complete, $resumeHadith null hi rahega
                }
            }
        }

        return view('hadith.chapters', compact('collection', 'chapters', 'resumeHadith', 'hasReadAnything'));
    }




    public function show(HadithCollection $collection, HadithChapter $chapter, Request $request)
    {
        $hadiths = Hadith::where('collection_id', $collection->id)
            ->where('chapter_id', $chapter->id)
            ->orderBy('number')
            ->take(self::PER_PAGE)
            ->get(['id', 'number', 'arabic', 'english', 'grade', 'grade_source']);

        $highlightId = $request->query('highlight'); // e.g. ?highlight=4213

        $targetPage = null;
        $targetHadithId = null;

        if ($highlightId) {
            $targetHadithId = (int) $highlightId;

            $targetHadith = Hadith::find($targetHadithId);

            if ($targetHadith) {
                $position = Hadith::where('chapter_id', $chapter->id)
                    ->where('number', '<=', $targetHadith->number)
                    ->count();

                $targetPage = (int) ceil($position / self::PER_PAGE);
            }
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $bookmarkedIds = [];
        $userNotes = collect();
        $readIds = [];

        if ($user) {
            $hadithIds = $hadiths->pluck('id');

            $bookmarkedIds = \App\Models\Bookmark::where('user_id', $user->id)
                ->where('bookmarkable_type', \App\Models\Hadith::class)
                ->whereIn('bookmarkable_id', $hadithIds)
                ->pluck('bookmarkable_id')
                ->toArray();

            $userNotes = \App\Models\Note::where('user_id', $user->id)
                ->whereIn('hadith_id', $hadithIds)
                ->get()
                ->keyBy('hadith_id');

            $readIds = $user->readHadiths()->pluck('hadiths.id')->toArray();
        }

        return view('hadith.show', compact(
            'collection',
            'chapter',
            'hadiths',
            'bookmarkedIds',
            'userNotes',
            'targetPage',
            'targetHadithId',
            'readIds'
        ));
    }

    public function loadHadiths(Request $request, HadithCollection $collection, HadithChapter $chapter)
    {
        $page = $request->get('page', 1);

        $hadiths = Hadith::where('collection_id', $collection->id)
            ->where('chapter_id', $chapter->id)
            ->orderBy('number')
            ->skip(($page - 1) * self::PER_PAGE)
            ->take(self::PER_PAGE)
            ->get(['id', 'number', 'arabic', 'english', 'grade', 'grade_source']);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $bookmarkedIds = [];
        $readIds = [];

        if ($user) {
            $hadithIds = $hadiths->pluck('id');

            $bookmarkedIds = \App\Models\Bookmark::where('user_id', $user->id)
                ->where('bookmarkable_type', \App\Models\Hadith::class)
                ->whereIn('bookmarkable_id', $hadithIds)
                ->pluck('bookmarkable_id')
                ->toArray();

            $readIds = $user->readHadiths()->whereIn('hadiths.id', $hadithIds)->pluck('hadiths.id')->toArray();
        }

        $hadiths->each(function ($h) use ($bookmarkedIds, $readIds) {
            $h->is_bookmarked = in_array($h->id, $bookmarkedIds);
            $h->is_read = in_array($h->id, $readIds);
        });

        return response()->json([
            'hadiths' => $hadiths,
            'has_more' => $hadiths->count() === self::PER_PAGE,
        ]);
    }

    // ✅ Naya method — Mark as Read toggle
    public function toggleRead(Request $request, Hadith $hadith)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $exists = $user->readHadiths()->where('hadith_id', $hadith->id)->exists();

        if ($exists) {
            $user->readHadiths()->detach($hadith->id);
            $isRead = false;
        } else {
            $user->readHadiths()->attach($hadith->id, ['read_at' => now()]);
            $isRead = true;
        }

        return response()->json(['is_read' => $isRead]);
    }
}
