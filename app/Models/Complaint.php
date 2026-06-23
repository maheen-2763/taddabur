<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'category',
        'status',
        'admin_reply',
        'replied_at',
        'resolved_at',
    ];

    protected $casts = [
        'replied_at'  => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ───────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopePayment($query)
    {
        return $query->where('category', 'payment');
    }

    // ── Helpers ──────────────────────────────────────

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function hasReply(): bool
    {
        return ! is_null($this->admin_reply);
    }
}
