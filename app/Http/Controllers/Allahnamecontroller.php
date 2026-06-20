<?php
// app/Http/Controllers/AllahNameController.php
//
// PURPOSE: Serves the full 99 Names of Allah honeycomb page.
//          Public route — no login required, since teaching
//          Allah's Names benefits everyone, including guests.

namespace App\Http\Controllers;

use App\Models\AllahName;
use Illuminate\View\View;

class AllahNameController extends Controller
{
    // GET /allah-names
    public function index(): View
    {
        $names = AllahName::orderBy('position')->get();

        // Split into rows of 11 → 9 rows × 11 = 99
        // Used by the blade to build the honeycomb tessellation
        $rows = $names->chunk(11);

        return view('allah-names.index', compact('names', 'rows'));
    }
}
