<?php

namespace App\Billing;

use App\Models\Plan;
use Carbon\Carbon;

/**
 * Read-only view of the user's billing state for UI and quotas (not an Eloquent model).
 */
final class SubscriptionPresentation
{
    public function __construct(
        public ?Plan $plan,
        public string $type,
        public ?Carbon $ends_at,
    ) {}
}
