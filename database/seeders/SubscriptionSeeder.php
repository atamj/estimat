<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'jael@example.com')->firstOrFail();
        $pioneerPlan = Plan::where('slug', 'pioneer')->firstOrFail();

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $pioneerPlan->id,
            'type' => 'lifetime',
            'status' => 'active',
            'starts_at' => now(),
        ]);
    }
}
