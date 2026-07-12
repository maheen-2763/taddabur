<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = ['from_currency', 'to_currency', 'rate', 'fetched_at'];

    protected $casts = [
        'rate' => 'decimal:4',
        'fetched_at' => 'datetime',
    ];

    public static function usdToInr(): float
    {
        $row = static::where('from_currency', 'USD')
            ->where('to_currency', 'INR')
            ->first();

        // Safety net: if the daily job has never run yet or somehow
        // the row is missing, fall back to a known-reasonable rate
        // instead of crashing checkout entirely.
        return $row?->rate ? (float) $row->rate : 83.00;
    }
}
