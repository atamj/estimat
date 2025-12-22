<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'expires_at',
        'max_uses', 'uses_count', 'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
