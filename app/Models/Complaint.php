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

    public const USER_CATEGORIES = [
        'content' => 'Content (Quran, stories, hadith)',
        'account' => 'Account / login',
        'bug'     => 'Bug (nothing is working)',
        'other'   => 'Other',
    ];


    public const STATUSES = ['open', 'in_progress', 'resolved'];

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open'        => 'Open',
            'in_progress' => 'In progress',
            'resolved'    => 'Resolved',
            default       => ucfirst($this->status),
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'open'        => 'bg-warning text-dark',
            'in_progress' => 'bg-info text-dark',
            'resolved'    => 'bg-success',
            default       => 'bg-secondary',
        };
    }

    public function categoryLabel(): string
    {
        return self::USER_CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

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
