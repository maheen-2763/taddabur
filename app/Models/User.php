<?php

namespace App\Models;

use App\Enums\PlanType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plan',
        'plan_expires_at',
        'preferred_language',
        'preferred_translation',
        'preferred_tafsir',
        'preferred_reciter',
        'avatar',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'plan_expires_at' => 'datetime',
        'is_admin' => 'boolean',
        'password' => 'hashed',

        // Optional if using Enum
        // 'plan' => PlanType::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->latestOfMany();
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function planModel(): BelongsTo
    {
        return $this->belongsTo(
            Plan::class,
            'plan',
            'slug'
        );
    }


    public function scopeForUser($query, User $user)
    {
        return $query->where('id', $user->id);
    }

    /*
    |--------------------------------------------------------------------------
    | Subscription Helpers
    |--------------------------------------------------------------------------
    */

    public function isPremium(): bool
    {
        return $this->plan === 'premium'
            && (
                $this->plan_expires_at === null
                || $this->plan_expires_at->isFuture()
            );
    }

    public function hasActiveSubscription(): bool
    {
        return $this->isPremium();
    }

    public function canAccess(string $feature): bool
    {
        return (bool) data_get(
            $this->planModel,
            $feature
        );
    }

    public function hasFeature(string $feature): bool
    {
        return (bool) optional(
            $this->subscription?->plan
        )->{$feature};
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? Storage::url($this->avatar)
            : asset('images/default-avatar.png');
    }
}
