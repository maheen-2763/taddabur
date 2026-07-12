<?php
// app/Models/Subscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_order_id',      // NEW — links back to the order that paid for this
        'type',
        'razorpay_payment_id',   // renamed from stripe_id
        'status',                // renamed from stripe_status
        'quantity',
        'trial_ends_at',
        'ends_at',
    ];
    // Note: 'stripe_price' removed — it was dropped in the migration
    // since it was never actually used anywhere in the Razorpay flow.

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at'       => 'datetime',
    ];

    // -------------------------------------------------------
    // RELATIONSHIPS
    // -------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function paymentOrder(): BelongsTo
    {
        return $this->belongsTo(PaymentOrder::class);
    }

    // -------------------------------------------------------
    // HELPER METHODS
    // -------------------------------------------------------

    // Is this subscription currently active?
    public function isActive(): bool
    {
        return $this->status === 'active'   // was stripe_status
            && (
                !$this->ends_at ||
                $this->ends_at->isFuture()
            );
    }

    // Is this subscription on trial?
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
}
