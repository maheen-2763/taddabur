<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'duration_minutes',
        'platform',
    ];

    protected $casts = [
        'started_at'       => 'datetime',
        'ended_at'         => 'datetime',
        'duration_minutes' => 'integer',
    ];

    // ── Relationships ────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ───────────────────────────────────────

    public function scopeCompleted($query)
    {
        // Sessions that have ended
        return $query->whereNotNull('ended_at');
    }

    public function scopeActive($query)
    {
        // Sessions still in progress
        return $query->whereNull('ended_at');
    }

    public function scopeLastDays($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeLastMonth($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }

    public function scopeForPlan($query, string $plan)
    {
        return $query->whereHas('user', fn($q) => $q->where('plan', $plan));
    }

    public function scopeWeb($query)
    {
        return $query->where('platform', 'web');
    }

    public function scopeMobile($query)
    {
        return $query->where('platform', 'mobile');
    }

    // ── Helpers ──────────────────────────────────────

    /**
     * Call this on logout or session timeout.
     * Calculates and stores duration automatically.
     */
    public function finish(): void
    {
        $this->ended_at         = now();
        $this->duration_minutes = (int) $this->started_at->diffInMinutes(now());
        $this->save();
    }

    public function isActive(): bool
    {
        return is_null($this->ended_at);
    }

    public function durationForHumans(): string
    {
        if ($this->duration_minutes < 60) {
            return "{$this->duration_minutes} min";
        }

        $hours   = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        return $minutes > 0
            ? "{$hours}h {$minutes}m"
            : "{$hours}h";
    }
}
