<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'admin@estimat.pro';

    public const ADMIN_NAME = 'Admin';

    public const ADMIN_PASSWORD = 'password';

    public const ADMIN_IS_ADMIN = true;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => self::ADMIN_EMAIL],
            [
                'name' => self::ADMIN_NAME,
                'password' => self::ADMIN_PASSWORD,
                'is_admin' => self::ADMIN_IS_ADMIN,
            ],
        );
    }
}
