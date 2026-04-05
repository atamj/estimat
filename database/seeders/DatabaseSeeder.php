<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

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
        ]);

        $admin = User::query()->where('email', AdminUserSeeder::ADMIN_EMAIL)->firstOrFail();
        Auth::loginUsingId($admin->id);

        $this->call([
            AdminProjectTypeSeeder::class,
            AdminSetupSeeder::class,
            AdminBlockSeeder::class,
            AdminOptionSeeder::class,
            AdminTranslationConfigSeeder::class,
        ]);
    }
}
