<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'rating',
        'comment',
        'category',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // ── Relationships ────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ───────────────────────────────────────

    public function scopePositive($query)
    {
        // 4 and 5 star
        return $query->where('rating', '>=', 4);
    }

    public function scopeNegative($query)
    {
        // 1 and 2 star — worth monitoring
        return $query->where('rating', '<=', 2);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Helpers ──────────────────────────────────────

    public function stars(): string
    {
        return str_repeat('★', $this->rating)
            . str_repeat('☆', 5 - $this->rating);
    }

    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }
}
