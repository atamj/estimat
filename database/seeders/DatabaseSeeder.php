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
        $this->seedAdminData();

        $this->call([
            DemoUserSeeder::class,
            PlanSeeder::class,
            SubscriptionSeeder::class,
        ]);
    }

    public function seedAdminData(): void
    {
        $this->call([
            AdminUserSeeder::class,
            AdminProjectTypeSeeder::class,
            AdminSetupSeeder::class,
            AdminBlockSeeder::class,
            AdminOptionSeeder::class,
            AdminTranslationConfigSeeder::class,
        ]);
    }
}
