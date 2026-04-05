<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminProjectTypeSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::where('email', AdminUserSeeder::ADMIN_EMAIL)->firstOrFail()->id;

        ProjectType::create([
            'user_id' => $userId,
            'name' => 'WordPress',
            'icon' => 'fab-wordpress',
            'description' => 'Site sous CMS WordPress (Elementor, Gutenberg, etc.)',
            'is_default' => false,
        ]);

        ProjectType::create([
            'user_id' => $userId,
            'name' => 'Bedrock',
            'icon' => 'fab-wordpress',
            'description' => 'Site sous CMS WordPress avec le boilerplate (modèle) Bedrock qui permet de gérer WordPress avec Composer et Git et de façon plus sécurisé.',
            'is_default' => true,
        ]);

        ProjectType::create([
            'user_id' => $userId,
            'name' => 'Laravel',
            'icon' => 'fab-laravel',
            'description' => 'Application métier sur mesure performante',
            'is_default' => false,
        ]);

        ProjectType::create([
            'user_id' => $userId,
            'name' => 'PHP / Symfony',
            'icon' => 'fab-php',
            'description' => 'Développement spécifique PHP ou framework Symfony',
            'is_default' => false,
        ]);
    }
}
