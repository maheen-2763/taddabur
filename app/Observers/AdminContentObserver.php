<?php

namespace App\Observers;

use App\Actions\RecordActivity;
use App\Enums\ActivityEvent;
use App\Models\DailyContent;
use App\Models\Prophet;
use App\Models\Story;
use App\Models\StoryChapter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Records content changes made by a signed-in admin.
 * Console commands, seeders, schedulers and regular users never produce log rows.
 */
final class AdminContentObserver
{
    /** Models whose changes are logged. */
    public const MODELS = [Story::class, StoryChapter::class, Prophet::class, DailyContent::class];

    /** Columns that change automatically and are not worth logging on their own. */
    private const IGNORED_FIELDS = ['updated_at'];

    public function __construct(private readonly RecordActivity $record) {}

    public function created(Model $model): void
    {
        $this->log(ActivityEvent::ContentCreated, $model);
    }

    public function updated(Model $model): void
    {
        $fields = array_values(array_diff(array_keys($model->getChanges()), self::IGNORED_FIELDS));

        if ($fields === []) {
            return;
        }

        $this->log(ActivityEvent::ContentUpdated, $model, ['fields' => implode(', ', $fields)]);
    }

    public function deleted(Model $model): void
    {
        $this->log(ActivityEvent::ContentDeleted, $model);
    }

    public function restored(Model $model): void
    {
        $this->log(ActivityEvent::ContentRestored, $model);
    }

    private function log(ActivityEvent $event, Model $model, array $properties = []): void
    {
        $user = Auth::user();

        if (! $user || ! $user->is_admin) {
            return;
        }

        $this->record->handle($event, (int) $user->getAuthIdentifier(), [
            'subject' => Str::snake(class_basename($model)),
            'subject_id' => $model->getKey(),
        ] + $properties);
    }
}
