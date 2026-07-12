<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use App\Models\HadithChapter;
use App\Models\HadithCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HadithController extends Controller
{
    // ✅ Page 1: Saari Collections (Bukhari, Muslim, etc.)
    public function index()
    {
        $collections = HadithCollection::all();
        return view('hadith.index', compact('collections'));
    }

    public function chapters(HadithCollection $collection)
    {
        // ✅ chapters table mein stored count nahi hai, isliye withCount zaroori
        $chapters = HadithChapter::where('collection_id', $collection->id)
            ->withCount('hadiths')
            ->orderBy('number')
            ->get();

        return view('hadith.chapters', compact('collection', 'chapters'));
    }

    public function show(HadithCollection $collection, HadithChapter $chapter, Request $request)
    {
        $perPage = 20;

        $hadiths = Hadith::where('collection_id', $collection->id)
            ->where('chapter_id', $chapter->id)
            ->orderBy('number')
            ->take($perPage)
            ->get(['id', 'number', 'arabic', 'english', 'grade', 'grade_source']);

        $targetHadithNumber = $request->query('highlight'); // e.g. ?highlight=4213
        $targetPage = null;

        if ($targetHadithNumber) {
            $position = Hadith::where('collection_id', $collection->id)
                ->where('chapter_id', $chapter->id)
                ->where('number', '<=', $targetHadithNumber)
                ->count();

            $targetPage = (int) ceil($position / $perPage);
        }

        $user = Auth::user();
        $bookmarkedIds = [];
        $userNotes = collect();

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
        }

        return view('hadith.show', compact(
            'collection',
            'chapter',
            'hadiths',
            'bookmarkedIds',
            'userNotes',
            'targetPage',
            'targetHadithNumber'
        ));
    }

    public function loadHadiths(Request $request, HadithCollection $collection, HadithChapter $chapter)
    {
        $page = $request->get('page', 1);

        $hadiths = Hadith::where('collection_id', $collection->id)
            ->where('chapter_id', $chapter->id)
            ->orderBy('number')
            ->skip(($page - 1) * 20)
            ->take(20)
            ->get(['id', 'number', 'arabic', 'english', 'grade', 'grade_source']);

        $user = Auth::user();
        $bookmarkedIds = [];

        if ($user) {
            $bookmarkedIds = \App\Models\Bookmark::where('user_id', $user->id)
                ->where('bookmarkable_type', \App\Models\Hadith::class)
                ->whereIn('bookmarkable_id', $hadiths->pluck('id'))
                ->pluck('bookmarkable_id')
                ->toArray();
        }

        // ✅ Frontend ko batao kaunse already bookmarked hain
        $hadiths->each(function ($h) use ($bookmarkedIds) {
            $h->is_bookmarked = in_array($h->id, $bookmarkedIds);
        });

        return response()->json([
            'hadiths' => $hadiths,
            'has_more' => $hadiths->count() === 20,
        ]);
    }
}
