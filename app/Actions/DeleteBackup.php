<?php

namespace App\Actions;

use App\Models\Backup;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class DeleteBackup
{
    /** Deletes the file first, then the row. If the file cannot be deleted the row is kept and false is returned. */
    public function handle(Backup $backup): bool
    {
        $path = $backup->storagePath();

        if (Storage::exists($path) && ! Storage::delete($path)) {
            Log::error('Backup file could not be deleted', ['backup_id' => $backup->id]);

            return false;
        }

        $backup->delete();

        return true;
    }
}
