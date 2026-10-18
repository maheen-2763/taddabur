<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyContent;
use App\Models\Surah;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDailyReflectionController extends Controller
{
    public function index(): View
    {
        $dailyContents = DailyContent::query()
            ->with(['ayah.surah'])
            ->latest('scheduled_for')
            ->paginate(20);

        return view('admin.daily-reflections.index', compact('dailyContents'));
    }

    public function create(): View
    {
        $surahs = Surah::orderBy('number')->get(['id', 'number', 'name_transliteration']);

        return view('admin.daily-reflections.create', compact('surahs'));
    }

    public function ayahs(Surah $surah): JsonResponse
    {
        return response()->json($surah->ayahs()->get(['id', 'number']));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scheduled_for' => [
                'required',
                'date',
                function (string $attribute, mixed $value, Closure $fail) {
                    // whereDate: purane rows jinme time bhi saved hai, unhe bhi pakadta hai
                    $exists = DailyContent::where('type', 'ayah')
                        ->whereDate('scheduled_for', $value)
                        ->exists();

                    if ($exists) {
                        $fail('Is date ke liye ayah reflection pehle se maujood hai.');
                    }
                },
            ],
            'ayah_id'       => ['required', 'integer', 'exists:ayahs,id'],
            'reflection'    => ['required', 'string', 'min:50', 'max:5000'],
        ]);

        DailyContent::create([
            'type'          => 'ayah',
            'ayah_id'       => $validated['ayah_id'],
            'scheduled_for' => $validated['scheduled_for'],
            'reflection'    => $validated['reflection'],
        ]);

        return redirect()
            ->route('admin.daily-reflections.index')
            ->with('success', 'Daily reflection created successfully.');
    }

    public function destroy(DailyContent $dailyReflection): RedirectResponse
    {
        $dailyReflection->delete();

        return redirect()
            ->route('admin.daily-reflections.index')
            ->with('success', 'Entry deleted successfully.');
    }
}
