<?php

namespace App\Http\Controllers;

use App\Models\Hadith;
use Illuminate\View\View;

class HadithGradeController extends Controller
{
    private const VALID_RELIABILITIES = ['Sahih', 'Hasan', 'Daif', 'Very Daif', 'Mawdu', 'Munkar', 'Shadh'];

    public function show(string $reliability): View
    {
        // Security + correctness: sirf known values allow karo, random string se DB query na chale
        abort_unless(in_array($reliability, self::VALID_RELIABILITIES), 404);

        $hadiths = Hadith::where('reliability', $reliability)
            ->with('collection', 'chapter')
            ->latest()
            ->paginate(20);

        return view('hadith.grade', [
            'reliability' => $reliability,
            'hadiths' => $hadiths,
        ]);
    }
}
