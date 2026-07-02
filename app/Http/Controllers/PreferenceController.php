<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPreference;

class PreferenceController extends Controller
{
    public function updateFontSize(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'size_index' => 'required|integer|min:0|max:4',
        ]);

        UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            ['quran_font_size_index' => $request->size_index]
        );

        return response()->json(['success' => true]);
    }
}
