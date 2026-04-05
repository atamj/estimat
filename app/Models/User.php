<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Billing\SubscriptionPresentation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'default_currency',
        'plan_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    /**
     * Effective plan: active Stripe subscription (Cashier) or local plan_id (free, lifetime one-shot, admin).
     */
    public function currentPlan(): ?Plan
    {
        $cashierSub = $this->subscription('default');
        if ($cashierSub && $cashierSub->valid()) {
            $priceId = $cashierSub->stripe_price ?? $cashierSub->items->first()?->stripe_price;
            $resolved = Plan::findByStripePriceId($priceId);

            return $resolved;
        }

        if ($this->plan_id) {
            return $this->plan;
        }

        return null;
    }

    public function getActivePlanAttribute(): ?Plan
    {
        return $this->currentPlan();
    }

    /**
     * Presentation for UI (type, renewal) — not the Cashier Eloquent model.
     * Kept private so Laravel does not treat it as an Eloquent relationship when accessed as a property.
     */
    private function resolveSubscriptionPresentation(): ?SubscriptionPresentation
    {
        $cashierSub = $this->subscription('default');
        if ($cashierSub && $cashierSub->valid()) {
            $priceId = $cashierSub->stripe_price ?? $cashierSub->items->first()?->stripe_price;
            $plan = Plan::findByStripePriceId($priceId);
            if (! $plan) {
                return null;
            }
            $type = Plan::billingCycleTypeFromPrice($plan, $priceId);

            return new SubscriptionPresentation($plan, $type, $cashierSub->ends_at);
        }

        if ($this->plan_id && $this->plan) {
            $plan = $this->plan;
            $type = ($plan->price_monthly == 0 && $plan->price_yearly == 0)
                ? 'free'
                : ($plan->price_lifetime ? 'lifetime' : 'monthly');

            return new SubscriptionPresentation($plan, $type, null);
        }

        return null;
    }

    public function getActiveSubscriptionAttribute(): ?SubscriptionPresentation
    {
        return $this->resolveSubscriptionPresentation();
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->subscription('default')?->valid()) {
            return true;
        }

        return $this->plan_id && $this->plan;
    }
}
