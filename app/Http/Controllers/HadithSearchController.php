<?php

namespace App\Http\Controllers;

use App\Helpers\ArabicHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HadithSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return view('hadith.search', ['results' => collect(), 'query' => $query]);
        }

        // Arabic query bhi normalize karo taaki tashkeel-mismatch na ho
        $normalizedQuery = ArabicHelper::normalizeArabic($query);

        // FTS5 special characters escape karo (", *, - etc user tod sakte hain query)
        $safeQuery = '"' . str_replace('"', '""', $query) . '"';
        $safeNormalizedQuery = '"' . str_replace('"', '""', $normalizedQuery) . '"';

        $results = DB::table('hadiths_fts')
            ->select('hadiths.*')
            ->join('hadiths', 'hadiths.id', '=', 'hadiths_fts.rowid')
            ->join('hadith_chapters', 'hadith_chapters.id', '=', 'hadiths.chapter_id')
            ->join('hadith_collections', 'hadith_collections.id', '=', 'hadiths.collection_id')
            ->addSelect([
                'hadith_chapters.number as chapter_number',
                'hadith_collections.slug as collection_slug',
            ])
            ->whereRaw('hadiths_fts MATCH ?', ["arabic_normalized:{$safeNormalizedQuery} OR english:{$safeQuery}"])
            ->limit(30)
            ->get();

        $results->transform(function ($hadith) use ($query) {
            $hadith->arabic_highlighted = $this->highlightMatch($hadith->arabic, $query);
            $hadith->english_highlighted = $this->highlightMatch($hadith->english, $query);
            return $hadith;
        });

        return view('hadith.search', [
            'results' => $results,
            'query' => $query,
        ]);
    }




    // HadithSearchController.php

    private function highlightMatch(string $text, string $query): string
    {
        if (mb_strlen($query) < 2) {
            return e($text);
        }

        // Text ko pehle escape karo (XSS-safe), phir match wrap karo
        $escaped = e($text);
        $escapedQuery = e($query);

        return preg_replace(
            '/(' . preg_quote($escapedQuery, '/') . ')/iu',
            '<mark class="search-highlight">$1</mark>',
            $escaped
        );
    }
}
