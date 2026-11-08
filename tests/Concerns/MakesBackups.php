<?php

namespace Tests\Concerns;

use App\Models\Backup;
use Illuminate\Support\Facades\Storage;

trait MakesBackups
{
    private int $backupSeq = 0;

    protected function makeBackup(string $type = 'auto', bool $withFile = true): Backup
    {
        $filename = sprintf('taddabur_backup_20260101_%06d_%s.zip', ++$this->backupSeq, $type);

        if ($withFile) {
            Storage::put("backups/{$filename}", 'x');
        }

        return Backup::create(['filename' => $filename, 'size' => '1 B']);
    }
}
