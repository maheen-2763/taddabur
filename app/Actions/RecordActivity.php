<?php

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use Illuminate\Support\Arr;

final class RecordActivity
{
    private const ALLOWED_PROPERTIES = ['reason', 'count'];

    public function handle(ActivityEvent $event, ?int $actorId = null, array $properties = []): ActivityLog
    {
        $safe = array_filter(
            Arr::only($properties, self::ALLOWED_PROPERTIES),
            'is_scalar',
        );

        return ActivityLog::create([
            'event' => $event->value,
            'actor_id' => $actorId,
            'properties' => $safe ?: null,
        ]);
    }
}
