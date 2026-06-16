<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'price_lifetime',
        'stripe_monthly_price_id',
        'stripe_yearly_price_id',
        'features',
        'story_limit',
        'translation_limit',
        'has_tafsir',
        'has_audio',
        'has_notes',
        'has_progress',
        'has_downloads',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features'       => 'array',
        'has_tafsir'     => 'boolean',
        'has_audio'      => 'boolean',
        'has_notes'      => 'boolean',
        'has_progress'   => 'boolean',
        'has_downloads'  => 'boolean',
        'is_active'      => 'boolean',
        'price_monthly'  => 'decimal:2',
        'price_yearly'   => 'decimal:2',
        'price_lifetime' => 'decimal:2',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // ── SCOPES ────────────────────────────────────────

    // Plan::active()->get()
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // Plan::active()->free()->get()
    public function scopeFree($query)
    {
        return $query->where('slug', 'free');
    }

    // -------------------------------------------------------
    // ACCESSORS
    // -------------------------------------------------------

    // "Free", "9.99/mo", etc.
    public function getFormattedMonthlyPriceAttribute(): string
    {
        if ($this->price_monthly == 0) {
            return 'Free';
        }

        return '$' . number_format($this->price_monthly, 2) . '/mo';
    }

    public function getFormattedYearlyPriceAttribute(): string
    {
        if ($this->price_yearly == 0) {
            return 'Free';
        }

        return '$' . number_format($this->price_yearly, 2) . '/yr';
    }

    // Yearly saving vs monthly * 12
    public function getYearlySavingsAttribute(): float
    {
        return ($this->price_monthly * 12) - $this->price_yearly;
    }

    // Is this the free plan?
    public function isFree(): bool
    {
        return $this->price_monthly == 0;
    }

    // Add to Plan.php

    public function getStoryLimitLabelAttribute(): string
    {
        return $this->story_limit === null ? 'Unlimited' : (string) $this->story_limit;
    }

    public function getTranslationLimitLabelAttribute(): string
    {
        return $this->translation_limit === null ? 'All' : (string) $this->translation_limit;
    }

    // app/Models/Plan.php

    // What each plan gets
    public static function featureLimits(): array
    {
        return [
            'free' => [
                'translations' => 1,   // Only Sahih International
                'tafsirs'      => 0,   // No tafsir
                'reciters'     => 1,   // Only Mishary
                'has_tafsir'   => false,
                'has_audio'    => true,  // 1 reciter only
                'has_notes'    => false,
            ],
            'basic' => [
                'translations' => -1,  // unlimited
                'tafsirs'      => -1,
                'reciters'     => -1,
                'has_tafsir'   => true,
                'has_audio'    => true,
                'has_notes'    => false,
            ],
            'premium' => [
                'translations' => -1,
                'tafsirs'      => -1,
                'reciters'     => -1,
                'has_tafsir'   => true,
                'has_audio'    => true,
                'has_notes'    => true,
            ],
        ];
    }
}
