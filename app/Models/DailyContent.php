<?php
// app/Models/DailyContent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'ayah_id',
        'story_id',
        'scheduled_for',
        'reflection',
        'is_sent',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'is_sent'       => 'boolean',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function ayah(): BelongsTo
    {
        return $this->belongsTo(Ayah::class);
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    // -------------------------------------------------------
    // SCOPES
    // -------------------------------------------------------

    // Get today's content
    public function scopeToday($query)
    {
        return $query->whereBetween('scheduled_for', [
            today()->startOfDay(),
            today()->endOfDay()
        ]);
    }

    // Get unsent content (for the email scheduler)
    public function scopePending($query)
    {
        return $query->where('is_sent', false)
            ->where('scheduled_for', '<=', today());
    }
}
