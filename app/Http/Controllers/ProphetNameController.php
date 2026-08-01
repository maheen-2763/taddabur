<?php

namespace App\Http\Controllers;

use App\Models\ProphetName;

class ProphetNameController extends Controller
{
    public function index()
    {
        $names = ProphetName::with('hadith.collection', 'hadith.chapter', 'ayah.surah') // 👈 ayah.surah add hua
            ->orderBy('sort_order')
            ->get()
            ->groupBy('tier');

        return view('prophet-names.index', [
            'names' => $names['name'] ?? collect(),
            'titles' => $names['title'] ?? collect(),
        ]);
    }
}
