<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prophet;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProphetController extends Controller
{
    public function index(): View
    {
        $prophets = Prophet::withCount('stories')->orderBy('order')->get();

        return view('admin.prophets.index', compact('prophets'));
    }

    public function edit(Prophet $prophet): View
    {
        return view('admin.prophets.edit', compact('prophet'));
    }

    public function update(Request $request, Prophet $prophet)
    {
        $request->validate([
            'summary'              => 'required|string',
            'title'                => 'nullable|string',
            'title_arabic'         => 'nullable|string',
            'title_transliteration' => 'nullable|string',
            'period'               => 'nullable|string',
            'mentioned_in_quran'   => 'nullable|string',
        ]);

        $prophet->update($request->only([
            'title',
            'title_arabic',
            'title_transliteration',
            'summary',
            'period',
            'mentioned_in_quran',
        ]));

        return back()->with('success', 'Prophet updated.');
    }
}
