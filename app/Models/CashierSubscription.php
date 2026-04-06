<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription as BaseSubscription;

class CashierSubscription extends BaseSubscription
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cashier_subscriptions';

    /**
     * Override to specify the explicit foreign key matching the migration column name.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Cashier::$subscriptionItemModel, 'subscription_id');
    }
}
