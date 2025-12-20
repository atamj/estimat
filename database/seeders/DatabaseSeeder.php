<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\ProjectType;
use App\Models\Setup;
use App\Models\Option;
use App\Models\TranslationConfig;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Project Types
        $wp = ProjectType::create(['name' => 'WordPress', 'icon' => 'fab-wordpress', 'description' => 'Site sous CMS WordPress']);
        $laravel = ProjectType::create(['name' => 'Laravel', 'icon' => 'fab-laravel', 'description' => 'Application métier sur mesure']);
        $scratch = ProjectType::create(['name' => 'From Scratch PHP', 'icon' => 'fab-php', 'description' => 'Développement spécifique']);

        // Setups
        Setup::create(['type' => 'Nouveau projet', 'amount' => 8]);
        Setup::create(['type' => 'Projet existant interne', 'amount' => 4]);
        Setup::create(['type' => 'Projet existant externe', 'amount' => 12]);

        // Blocs par défaut
        Block::create([
            'name' => 'Formulaire de contact',
            'description' => 'Formulaire standard avec validation et envoi par email',
            'type_unit' => 'hour',
            'price_programming' => 2,
            'price_integration' => 2,
            'price_field_creation' => 1,
            'price_content_management' => 0.5,
            'project_type_id' => $wp->id
        ]);

        Block::create([
            'name' => 'Espace Client (Laravel)',
            'description' => 'Zone sécurisée avec login et profil',
            'type_unit' => 'hour',
            'price_programming' => 8,
            'price_integration' => 4,
            'price_field_creation' => 4,
            'price_content_management' => 2,
            'project_type_id' => $laravel->id
        ]);

        Block::create([
            'name' => 'Blog / Actualités (Générique)',
            'description' => 'Listing et pages articles avec catégories',
            'type_unit' => 'hour',
            'price_programming' => 4,
            'price_integration' => 4,
            'price_field_creation' => 2,
            'price_content_management' => 3,
        ]);

        // Add-ons
        Option::create([
            'name' => 'Optimisation SEO (WP)',
            'type' => 'fixed_hours',
            'value' => 4,
            'description' => 'Plugins SEO et structure WP',
            'project_type_id' => $wp->id
        ]);

        Option::create([
            'name' => 'Maintenance annuelle (Générale)',
            'type' => 'percentage',
            'value' => 15,
            'calculation_base' => 'global',
            'description' => '15% du total global'
        ]);

        // Config Traduction par défaut (Générale)
        TranslationConfig::create([
            'default_fixed_amount' => 2,
            'default_percentage' => 10,
        ]);

        // Config Traduction WP
        TranslationConfig::create([
            'default_fixed_amount' => 5,
            'default_percentage' => 20,
            'project_type_id' => $wp->id
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
