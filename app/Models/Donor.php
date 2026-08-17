<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = ['name', 'amount', 'message', 'is_public'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
