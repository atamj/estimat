<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price_monthly', 'price_yearly',
        'price_lifetime', 'stripe_product_id', 'stripe_monthly_price_id',
        'stripe_yearly_price_id', 'stripe_lifetime_price_id',
        'max_estimations', 'max_blocks',
        'has_white_label_pdf', 'has_translation_module', 'is_active',
    ];

    protected $casts = [
        'has_white_label_pdf' => 'boolean',
        'has_translation_module' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'plan_id');
    }

    public static function findByStripePriceId(?string $priceId): ?self
    {
        if (! $priceId) {
            return null;
        }

        return static::query()
            ->where(function ($query) use ($priceId): void {
                $query->where('stripe_monthly_price_id', $priceId)
                    ->orWhere('stripe_yearly_price_id', $priceId)
                    ->orWhere('stripe_lifetime_price_id', $priceId);
            })
            ->first();
    }

    /**
     * @return 'monthly'|'yearly'|'lifetime'
     */
    public static function billingCycleTypeFromPrice(self $plan, string $priceId): string
    {
        if ($plan->stripe_monthly_price_id === $priceId) {
            return 'monthly';
        }
        if ($plan->stripe_yearly_price_id === $priceId) {
            return 'yearly';
        }
        if ($plan->stripe_lifetime_price_id === $priceId) {
            return 'lifetime';
        }

        return 'monthly';
    }
}
