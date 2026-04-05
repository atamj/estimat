<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public const JAEL_EMAIL = 'jael@example.com';

    public const JAEL_NAME = 'Jael';

    public const JAEL_PASSWORD = 'password';

    public const JAEL_IS_ADMIN = false;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => self::JAEL_EMAIL],
            [
                'name' => self::JAEL_NAME,
                'password' => self::JAEL_PASSWORD,
                'is_admin' => self::JAEL_IS_ADMIN,
            ],
        );
    }
}
