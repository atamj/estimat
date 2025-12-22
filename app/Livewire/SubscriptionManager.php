<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Estimation;
use App\Models\Block;
use App\Models\Plan;

class SubscriptionManager extends Component
{
    public function render()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription;
        $plan = $subscription ? $subscription->plan : null;

        $usage = [
            'estimations' => Estimation::where('user_id', $user->id)->count(),
            'blocks' => Block::where('user_id', $user->id)->count(),
        ];

        $availablePlans = Plan::where('is_active', true)->get();

        return view('livewire.subscription-manager', [
            'subscription' => $subscription,
            'plan' => $plan,
            'usage' => $usage,
            'availablePlans' => $availablePlans,
        ]);
    }
}
