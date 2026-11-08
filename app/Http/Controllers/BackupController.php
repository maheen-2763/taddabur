<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Actions\DeleteBackup;
use App\Actions\RecordActivity;
use App\Enums\ActivityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Number;


class BackupController extends Controller
{


    public function index()
    {
        $backups = Backup::latest('id')->paginate(20)->through(function (Backup $backup) {
            $backup->setAttribute('file_exists', Storage::exists($backup->storagePath()));

            return $backup;
        });

        return view('admin.backups', [
            'backups'  => $backups,
            'stats'    => $this->stats(),
            'latestId' => (int) Backup::max('id'),
        ]);
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

    public function download(Backup $backup, RecordActivity $record)
    {
        $path = $backup->storagePath();;

        abort_unless(Storage::exists($path), 404, 'Backup file missing');

        $record->handle(ActivityEvent::BackupDownloaded, Auth::id());

        Log::info('Backup downloaded', [
            'backup_id' => $backup->id,
            'filename'  => $backup->filename,
            'admin_id'  => Auth::id(),
            'ip'        => request()->ip(),
        ]);

        return Storage::download($path, basename($backup->filename));
    }

    public function destroyMany(Request $request, DeleteBackup $deleteBackup, RecordActivity $record): RedirectResponse
    {
        $data = $request->validate([
            'ids'   => ['required', 'array', 'max:50'],
            'ids.*' => ['integer', 'distinct', 'exists:backups,id'],
        ]);

        $latestId = (int) Backup::max('id');
        $deleted = $skipped = $failed = 0;

        foreach (Backup::whereIn('id', $data['ids'])->get() as $backup) {
            if ($backup->id === $latestId) {
                $skipped++;
                continue;
            }

            $deleteBackup->handle($backup) ? $deleted++ : $failed++;
        }

        if ($deleted > 0) {
            $record->handle(ActivityEvent::BackupDeleted, Auth::id(), ['count' => $deleted]);
        }

        $message = "{$deleted} Backups deleted .";
        if ($skipped > 0) {
            $message .= 'The newest backup is never deleted, so it was skipped.';
        }
        if ($failed > 0) {
            $message .= " {$failed} file(s) could not be deleted and were kept.";
        }

        return back()->with($failed === 0 && $deleted > 0 ? 'success' : 'error', $message);
    }

    private function stats(): array
    {
        $bytes = 0;
        $missing = 0;

        foreach (Backup::all() as $backup) {
            $path = $backup->storagePath();
            Storage::exists($path) ? $bytes += Storage::size($path) : $missing++;
        }

        return [
            'count'   => Backup::count(),
            'size'    => Number::fileSize($bytes, 2),
            'missing' => $missing,
        ];
    }
}
