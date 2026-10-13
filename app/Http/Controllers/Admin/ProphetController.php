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
        $total = Prophet::count();

        return view('admin.prophets.edit', compact('prophet', 'total'));
    }



    public function update(Request $request, Prophet $prophet)
    {
        $data = $request->validate([
            'summary'               => ['required', 'string'],
            'title'                 => ['nullable', 'string', 'max:255'],
            'title_arabic'          => ['nullable', 'string', 'max:255'],
            'title_transliteration' => ['nullable', 'string', 'max:255'],
            'period'                => ['nullable', 'string', 'max:255'],
            'mentioned_in_quran'    => ['nullable', 'string', 'max:1000'],
        ]);

        $prophet->update($data);

        return back()->with('success', 'Prophet updated.');
    }
}
