<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['event', 'actor_id', 'properties'];

    protected $casts = ['properties' => 'array', 'created_at' => 'datetime'];

    protected static function booted(): void
    {
        static::updating(fn() => throw new LogicException('Activity logs are append-only.'));
        static::deleting(fn() => throw new LogicException('Activity logs are append-only.'));
    }
}
