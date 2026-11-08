<?php

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Models\Backup;

final class PruneAutoBackups
{
    public function __construct(
        private readonly DeleteBackup $deleteBackup,
        private readonly RecordActivity $record,
    ) {}

    /** Keeps the newest $keep auto backups (at least 1). Manual backups are never touched. */
    public function handle(int $keep): int
    {
        $deleted = Backup::orderByDesc('id')->get()
            ->filter(fn(Backup $b) => $b->isAuto())
            ->skip(max(1, $keep))
            ->filter(fn(Backup $b) => $this->deleteBackup->handle($b))
            ->count();

        if ($deleted > 0) {
            $this->record->handle(ActivityEvent::BackupPruned, null, ['count' => $deleted]);
        }

        return $deleted;
    }
}
