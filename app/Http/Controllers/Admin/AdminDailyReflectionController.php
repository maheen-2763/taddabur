<?php

namespace App\Http\Controllers\Admin;

use App\Models\DailyContent;
use Illuminate\Http\Request;
use App\Models\Ayah;
use Illuminate\Validation\Rule;
use App\Models\Surah;
use App\Http\Controllers\Controller;


class AdminDailyReflectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $dailyContents = DailyContent::query()
            ->with(['ayah.surah'])
            ->latest('scheduled_for')
            ->paginate(20);

        return view(
            'admin.daily-reflections.index',
            compact('dailyContents')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $surahs = Surah::query()
            ->orderBy('number')
            ->get();

        return view(
            'admin.daily-reflections.create',
            compact('surahs')
        );
    }

    public function ayahs(Surah $surah)
    {
        return response()->json(

            $surah->ayahs()
                ->select(
                    'id',
                    'number'
                )
                ->orderBy('number')
                ->get()

        );
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'scheduled_for' => [
                'required',
                'date',
                Rule::unique('daily_contents', 'scheduled_for')
                    ->where(fn($query) => $query->where('type', 'ayah')),
            ],

            'ayah_id' => [
                'required',
                'exists:ayahs,id',
            ],

            'reflection' => ['required', 'string', 'min:50', 'max:5000']
        ]);

        DailyContent::create([
            'type' => 'ayah',
            'ayah_id' => $validated['ayah_id'],
            'scheduled_for' => $validated['scheduled_for'],
            'reflection' => $validated['reflection'],
        ]);

        return redirect()
            ->route('admin.daily-reflections.index')
            ->with(
                'success',
                'Daily reflection created successfully.'
            );
    }

    public function destroy(DailyContent $dailyReflection)
    {
        $dailyReflection->delete();

        return redirect()
            ->route('admin.daily-reflections.index')
            ->with(
                'success',
                'Reflection deleted successfully.'
            );
    }
}
