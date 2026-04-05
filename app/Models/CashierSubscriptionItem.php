<?php

namespace App\Models;

use Laravel\Cashier\SubscriptionItem as BaseSubscriptionItem;

class CashierSubscriptionItem extends BaseSubscriptionItem
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cashier_subscription_items';
}
