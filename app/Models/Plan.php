<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price_monthly', 'price_yearly',
        'price_lifetime', 'max_estimations', 'max_blocks',
        'has_white_label_pdf', 'has_translation_module', 'is_active'
    ];

    protected $casts = [
        'has_white_label_pdf' => 'boolean',
        'has_translation_module' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
