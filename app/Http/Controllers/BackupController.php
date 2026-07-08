<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::latest()->get();
        return view('admin.backups', compact('backups'));
    }

    public function download(Backup $backup)
    {
        $path = 'backups/' . $backup->filename;

        if (!Storage::exists($path)) {
            abort(404, 'Backup file missing');
        }

        return Storage::download($path);
    }
}
