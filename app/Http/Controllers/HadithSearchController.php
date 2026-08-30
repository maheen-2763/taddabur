<?php

namespace App\Http\Controllers;

use App\Helpers\ArabicHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HadithSearchController extends Controller
{
    const PER_PAGE = 15;

    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));

        if (mb_strlen($query) < 3) {
            return view('hadith.search', [
                'results' => collect(),
                'query' => $query,
                'bookmarkedIds' => [],
                'userNotes' => collect(),
                'readIds' => [],
            ]);
        }

        $normalizedQuery = ArabicHelper::normalizeArabic($query);

        $safeQuery = '"' . str_replace('"', '""', $query) . '"';
        $safeNormalizedQuery = '"' . str_replace('"', '""', $normalizedQuery) . '"';

        $results = DB::table('hadiths_fts')
            ->select('hadiths.*')
            ->join('hadiths', 'hadiths.id', '=', 'hadiths_fts.rowid')
            ->join('hadith_chapters', 'hadith_chapters.id', '=', 'hadiths.chapter_id')
            ->join('hadith_collections', 'hadith_collections.id', '=', 'hadiths.collection_id')
            ->addSelect([
                'hadith_chapters.number as chapter_number',
                'hadith_chapters.title as chapter_title',
                'hadith_collections.slug as collection_slug',
                'hadith_collections.name as collection_name',
            ])
            ->whereRaw('hadiths_fts MATCH ?', ["arabic_normalized:{$safeNormalizedQuery} OR english:{$safeQuery}"])
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // ✅ Highlight, applied to the current page's items only
        $results->getCollection()->transform(function ($hadith) use ($query) {
            $hadith->arabic_highlighted = $this->highlightMatch($hadith->arabic, $query);
            $hadith->english_highlighted = $this->highlightMatch($hadith->english, $query);
            return $hadith;
        });

        // ✅ Bookmark / Note / Read status — same pattern as HadithController@show
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $bookmarkedIds = [];
        $userNotes = collect();
        $readIds = [];

        if ($user) {
            $hadithIds = $results->pluck('id');

            $bookmarkedIds = \App\Models\Bookmark::where('user_id', $user->id)
                ->where('bookmarkable_type', \App\Models\Hadith::class)
                ->whereIn('bookmarkable_id', $hadithIds)
                ->pluck('bookmarkable_id')
                ->toArray();

            $userNotes = \App\Models\Note::where('user_id', $user->id)
                ->whereIn('hadith_id', $hadithIds)
                ->get()
                ->keyBy('hadith_id');

            $readIds = $user->readHadiths()->whereIn('hadiths.id', $hadithIds)->pluck('hadiths.id')->toArray();
        }

        return view('hadith.search', [
            'results' => $results,
            'query' => $query,
            'bookmarkedIds' => $bookmarkedIds,
            'userNotes' => $userNotes,
            'readIds' => $readIds,
        ]);
    }

    private function highlightMatch(string $text, string $query): string
    {
        if (mb_strlen($query) < 2) {
            return e($text);
        }

        $escaped = e($text);
        $escapedQuery = e($query);

        return preg_replace(
            '/(' . preg_quote($escapedQuery, '/') . ')/iu',
            '<mark class="search-highlight">$1</mark>',
            $escaped
        );
    }
}
