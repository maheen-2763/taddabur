<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use Illuminate\View\View;
use App\Models\HadithCollection;

class HadithGradeController extends Controller
{
    private const VALID_RELIABILITIES = ['Sahih', 'Hasan', 'Daif', 'Very Daif', 'Mawdu', 'Munkar', 'Shadh'];
    private const EXCLUDED_FROM_GRADING = ['bukhari', 'muslim'];

    private const PER_PAGE = 20;

    // Global — collections index page ke liye (purana wala, jaisa tha)
    public function show(string $reliability): View
    {
        abort_unless(in_array($reliability, self::VALID_RELIABILITIES), 404);

        $hadiths = Hadith::where('reliability', $reliability)
            ->with('collection', 'chapter')
            ->orderBy('number')
            ->paginate(self::PER_PAGE);

        return view('hadith.grade', [
            'collection' => null,          // view ko batayega ye global hai
            'reliability' => $reliability,
            'hadiths' => $hadiths,
        ]);
    }

    // Collection-scoped — collection ke andar se
    public function showForCollection(HadithCollection $collection, string $reliability): View
    {
        abort_if(in_array($collection->slug, self::EXCLUDED_FROM_GRADING), 404);
        abort_unless(in_array($reliability, self::VALID_RELIABILITIES), 404);

        $hadiths = Hadith::where('collection_id', $collection->id)
            ->where('reliability', $reliability)
            ->with('chapter')
            ->orderBy('number')
            ->paginate(self::PER_PAGE);

        return view('hadith.grade', [
            'collection' => $collection,
            'reliability' => $reliability,
            'hadiths' => $hadiths,
        ]);
    }
}
