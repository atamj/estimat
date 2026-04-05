<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'jael@example.com')->firstOrFail();
        $pioneerPlan = Plan::where('slug', 'pioneer')->firstOrFail();

        $user->update(['plan_id' => $pioneerPlan->id]);
    }
}
