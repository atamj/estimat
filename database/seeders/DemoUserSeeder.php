<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Option;
use App\Models\ProjectType;
use App\Models\Setup;
use App\Models\TranslationConfig;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Jael',
            'email' => 'jael@example.com',
            'is_admin' => false,
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@estimat.pro',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        Block::query()->update(['user_id' => $user->id]);
        ProjectType::query()->update(['user_id' => $user->id]);
        Setup::query()->update(['user_id' => $user->id]);
        Option::query()->update(['user_id' => $user->id]);
        TranslationConfig::query()->update(['user_id' => $user->id]);
    }
}
