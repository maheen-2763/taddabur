<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ActivityEvent;


class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['event', 'actor_id', 'properties'];

    protected $casts = ['properties' => 'array', 'created_at' => 'datetime'];


    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function eventEnum(): ?ActivityEvent
    {
        return ActivityEvent::tryFrom($this->event);
    }

    protected static function booted(): void
    {
        static::updating(fn() => throw new LogicException('Activity logs are append-only.'));
        static::deleting(fn() => throw new LogicException('Activity logs are append-only.'));
    }
}
