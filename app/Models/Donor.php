<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'message',
        'is_public',
        'razorpay_order_id',
        'razorpay_payment_id',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function scopePublic($query)
    {
        return $query->where('is_public', true)
            ->where('status', 'success'); // ye line add karo
    }
}
