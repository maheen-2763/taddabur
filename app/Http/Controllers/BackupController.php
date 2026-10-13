<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    private const DIRECTORY = 'backups';

    public function index()
    {
        $backups = Backup::latest('id')->paginate(20)->through(function (Backup $backup) {
            $backup->setAttribute('file_exists', Storage::exists($this->path($backup)));

            return $backup;
        });

        return view('admin.backups', compact('backups'));
    }

    public function run(): RedirectResponse
    {
        ignore_user_abort(true);
        set_time_limit(300);

        $exit   = Artisan::call('backup:run', ['--label' => 'manual', '--user' => Auth::id()]);
        $output = trim(Artisan::output());

        return redirect()
            ->route('admin.backups')
            ->with($exit === 0 ? 'success' : 'error', $output ?: 'Backup finished.');
    }

    public function download(Backup $backup)
    {
        $path = $this->path($backup);

        abort_unless(Storage::exists($path), 404, 'Backup file missing');

        Log::info('Backup downloaded', [
            'backup_id' => $backup->id,
            'filename'  => $backup->filename,
            'admin_id'  => Auth::id(),
            'ip'        => request()->ip(),
        ]);

        return Storage::download($path, basename($backup->filename));
    }

    private function path(Backup $backup): string
    {
        return self::DIRECTORY . '/' . basename($backup->filename);
    }
}
