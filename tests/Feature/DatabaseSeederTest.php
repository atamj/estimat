<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Plan;
use App\Models\Setup;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_completes_successfully(): void
    {
        $this->artisan('db:seed')->assertExitCode(0);

        $admin = User::query()->where('email', AdminUserSeeder::ADMIN_EMAIL)->firstOrFail();
        Auth::login($admin);

        $this->assertGreaterThan(0, Block::query()->count());
        $this->assertSame(3, Plan::query()->count());
        $this->assertNotNull(User::query()->where('email', 'jael@example.com')->first());
        $this->assertGreaterThan(0, Setup::query()->count());
        $this->assertSame(Setup::query()->count(), Setup::query()->whereNotNull('user_id')->count());
    }
}
