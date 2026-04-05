<?php

namespace App\Models;

use Laravel\Cashier\Subscription as BaseSubscription;

class CashierSubscription extends BaseSubscription
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cashier_subscriptions';
}
