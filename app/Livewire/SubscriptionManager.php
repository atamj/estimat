<?php

namespace App\Livewire;

use App\Models\Block;
use App\Models\Estimation;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionManager extends Component
{
    public function render()
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription;
        $plan = $subscription ? $subscription->plan : null;

        $usage = [
            'estimations' => Estimation::count(),
            'blocks' => Block::query()->count(),
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
