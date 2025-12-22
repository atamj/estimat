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
        // Nettoyage
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = OFF');
        \App\Models\ProjectType::truncate();
        \App\Models\Setup::truncate();
        \App\Models\Block::truncate();
        \App\Models\Option::truncate();
        \App\Models\TranslationConfig::truncate();
        \App\Models\User::truncate();
        \App\Models\Plan::truncate();
        \App\Models\Subscription::truncate();
        \App\Models\Coupon::truncate();
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = ON');

        // 1. Types de Projets (Technos)
        $wp = ProjectType::create([
            'name' => 'WordPress',
            'icon' => 'fab-wordpress',
            'description' => 'Site sous CMS WordPress (Elementor, Gutenberg, etc.)',
            'is_default' => false
        ]);

        $bedrock = ProjectType::create([
            'name' => 'Bedrock',
            'icon' => 'fab-wordpress',
            'description' => 'Site sous CMS WordPress avec le boilerplate (modèle) Bedrock qui permet de gérer WordPress avec Composer et Git et de façon plus sécurisé.',
            'is_default' => true
        ]);

        $laravel = ProjectType::create([
            'name' => 'Laravel',
            'icon' => 'fab-laravel',
            'description' => 'Application métier sur mesure performante'
        ]);

        $php = ProjectType::create([
            'name' => 'PHP / Symfony',
            'icon' => 'fab-php',
            'description' => 'Développement spécifique PHP ou framework Symfony'
        ]);

        // 2. Bases Techniques (Setups)
        // WordPress
        Setup::create(['project_type_id' => $bedrock->id, 'type' => 'Installation & Config WP', 'fixed_hours' => 8, 'fixed_price' => 0]);
        Setup::create(['project_type_id' => $bedrock->id, 'type' => 'Modifications projet interne', 'fixed_hours' => 0.5, 'fixed_price' => 0]);
        Setup::create(['project_type_id' => $bedrock->id, 'type' => 'Modifications projet externe', 'fixed_hours' => 1.5, 'fixed_price' => 0]);

        Setup::create(['project_type_id' => $wp->id, 'type' => 'Installation & Config WP', 'fixed_hours' => 12, 'fixed_price' => 0]);
        Setup::create(['project_type_id' => $wp->id, 'type' => 'Modifications projet interne', 'fixed_hours' => 1.5, 'fixed_price' => 0]);
        Setup::create(['project_type_id' => $wp->id, 'type' => 'Modifications projet externe', 'fixed_hours' => 2, 'fixed_price' => 0]);

        // Laravel
        Setup::create(['project_type_id' => $laravel->id, 'type' => 'Setup Laravel & DB', 'fixed_hours' => 8, 'fixed_price' => 0]);

        // Génériques
        Setup::create(['project_type_id' => null, 'type' => 'Nouveau projet standard', 'fixed_hours' => 6, 'fixed_price' => 300]);
        Setup::create(['project_type_id' => null, 'type' => 'Reprise de projet externe', 'fixed_hours' => 12, 'fixed_price' => 600]);

        // 3. Blocs
        // WordPress
        Block::create([
            'name' => 'Formulaire de contact (WP)',
            'description' => 'Formulaire avec Contact Form 7 ou WPForms',
            'type_unit' => 'hour',
            'price_programming' => 1,
            'price_integration' => 1,
            'price_field_creation' => 0.5,
            'price_content_management' => 0.5,
            'project_type_id' => $wp->id
        ]);

        Block::create([
            'name' => 'Installation WooCommerce',
            'description' => 'Installation et configuration de base e-commerce',
            'type_unit' => 'fixed',
            'price_programming' => 500,
            'price_integration' => 200,
            'price_field_creation' => 0,
            'price_content_management' => 0,
            'project_type_id' => $wp->id
        ]);

        // Laravel
        Block::create([
            'name' => 'Authentification & Profil (Laravel)',
            'description' => 'Système complet avec Breeze ou Jetstream',
            'type_unit' => 'hour',
            'price_programming' => 4,
            'price_integration' => 2,
            'price_field_creation' => 1,
            'price_content_management' => 1,
            'project_type_id' => $laravel->id
        ]);

        Block::create([
            'name' => 'Tableau de bord admin (Filament)',
            'description' => 'Interface de gestion CRUD complète',
            'type_unit' => 'hour',
            'price_programming' => 6,
            'price_integration' => 2,
            'price_field_creation' => 3,
            'price_content_management' => 2,
            'project_type_id' => $laravel->id
        ]);

        // Génériques
        Block::create([
            'name' => 'Page de contenu standard',
            'description' => 'Page simple avec texte et images',
            'type_unit' => 'hour',
            'price_programming' => 0,
            'price_integration' => 2,
            'price_field_creation' => 1,
            'price_content_management' => 1,
        ]);

        Block::create([
            'name' => 'Blog / Actualités',
            'description' => 'Listing et détail des articles',
            'type_unit' => 'hour',
            'price_programming' => 3,
            'price_integration' => 3,
            'price_field_creation' => 2,
            'price_content_management' => 2,
        ]);

        // 4. Add-ons (Options)
        Option::create([
            'name' => 'Optimisation SEO avancée',
            'type' => 'fixed_hours',
            'value' => 5,
            'description' => 'Recherche mots-clés et optimisation technique',
            'project_type_id' => $wp->id
        ]);

        Option::create([
            'name' => 'Formation administrateur',
            'type' => 'fixed_price',
            'value' => 450,
            'description' => 'Demi-journée de formation (distanciel)',
            'project_type_id' => null
        ]);

        Option::create([
            'name' => 'Maintenance annuelle',
            'type' => 'percentage',
            'value' => 15,
            'calculation_base' => 'global',
            'description' => '15% du total du devis',
            'project_type_id' => null
        ]);

        // 5. Config Traduction
        // WordPress (WPML ou Polylang)
        TranslationConfig::create([
            'project_type_id' => $wp->id,
            'default_fixed_hours' => 3,
            'default_fixed_price' => 0,
            'default_percentage' => 15,
        ]);

        // Laravel (i18n)
        TranslationConfig::create([
            'project_type_id' => $laravel->id,
            'default_fixed_hours' => 5,
            'default_fixed_price' => 0,
            'default_percentage' => 10,
        ]);

        // Défaut
        TranslationConfig::create([
            'project_type_id' => null,
            'default_fixed_hours' => 2,
            'default_fixed_price' => 150,
            'default_percentage' => 10,
        ]);

        // 6. Utilisateur de test
        $user = User::factory()->create([
            'name' => 'Jael',
            'email' => 'jael@example.com',
            'is_admin' => false,
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@estimat.pro',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // Assigner les données existantes à Jael
        \App\Models\Estimation::query()->update(['user_id' => $user->id]);
        \App\Models\Block::query()->update(['user_id' => $user->id]);
        \App\Models\ProjectType::query()->update(['user_id' => $user->id]);
        \App\Models\Setup::query()->update(['user_id' => $user->id]);
        \App\Models\Option::query()->update(['user_id' => $user->id]);
        \App\Models\TranslationConfig::query()->update(['user_id' => $user->id]);

        // 7. SaaS Plans
        $freePlan = \App\Models\Plan::create([
            'name' => 'Découverte',
            'slug' => 'free',
            'description' => 'Pour tester la puissance de l\'outil.',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'max_estimations' => 1,
            'max_blocks' => 5,
            'has_white_label_pdf' => false,
            'has_translation_module' => false,
        ]);

        $proPlan = \App\Models\Plan::create([
            'name' => 'Pro Indépendant',
            'slug' => 'pro',
            'description' => 'L\'outil indispensable pour chiffrer sereinement vos projets clients.',
            'price_monthly' => 19,
            'price_yearly' => 180,
            'max_estimations' => -1,
            'max_blocks' => -1,
            'has_white_label_pdf' => true,
            'has_translation_module' => true,
        ]);

        $pioneerPlan = \App\Models\Plan::create([
            'name' => 'Pionnier',
            'slug' => 'pioneer',
            'description' => 'Payez une fois, utilisez à vie.',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'price_lifetime' => 299,
            'max_estimations' => -1,
            'max_blocks' => -1,
            'has_white_label_pdf' => true,
            'has_translation_module' => true,
        ]);

        // 8. Abonnement par défaut pour Jael (Pionnier)
        \App\Models\Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $pioneerPlan->id,
            'type' => 'lifetime',
            'status' => 'active',
            'starts_at' => now(),
        ]);
    }
}
