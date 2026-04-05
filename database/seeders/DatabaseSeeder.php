<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProjectTypeSeeder::class,
            SetupSeeder::class,
            BlockSeeder::class,
            OptionSeeder::class,
            TranslationConfigSeeder::class,
            DemoUserSeeder::class,
            PlanSeeder::class,
            SubscriptionSeeder::class,
        ]);
    }
}
