<?php

namespace App\Livewire;

use App\Models\Block;
use App\Models\Estimation;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Livewire\Component;

class SubscriptionManager extends Component
{
    public function render()
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $plan = $subscription ? $subscription->plan : null;

        $paidPrice = $this->resolvePaidPrice($user, $subscription, $plan);

        $usage = [
            'estimations' => Estimation::count(),
            'blocks' => Block::query()->count(),
        ];

        $availablePlans = Plan::where('is_active', true)->get();

        return view('livewire.subscription-manager', [
            'subscription' => $subscription,
            'plan' => $plan,
            'paidPrice' => $paidPrice,
            'usage' => $usage,
            'availablePlans' => $availablePlans,
        ]);
    }

    /**
     * Resolve the actual price paid by the user.
     *
     * @return array{amount: float, cycle: string}|null
     */
    private function resolvePaidPrice($user, $subscription, ?Plan $plan): ?array
    {
        if (! $subscription || ! $plan) {
            return null;
        }

        if ($subscription->type === 'lifetime') {
            return ['amount' => (float) $plan->price_lifetime, 'cycle' => 'lifetime'];
        }

        if ($subscription->type === 'free') {
            return null;
        }

        $cashierSub = $user->subscription('default');
        if (! $cashierSub || ! $cashierSub->valid()) {
            return null;
        }

        $priceId = $cashierSub->stripe_price ?? $cashierSub->items->first()?->stripe_price;
        if (! $priceId) {
            return null;
        }

        $stripePrice = Cashier::stripe()->prices->retrieve($priceId);
        $amount = $stripePrice->unit_amount / 100;
        $cycle = $subscription->type === 'yearly' ? 'yearly' : 'monthly';

        return ['amount' => $amount, 'cycle' => $cycle];
    }
}
