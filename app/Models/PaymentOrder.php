<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentOrder extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'billing',
        'razorpay_order_id',
        'amount',
        'currency',
        'exchange_rate_used',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
