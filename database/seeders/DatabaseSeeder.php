<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Option;
use App\Models\ProjectType;
use App\Models\Setup;
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
            'is_default' => false,
        ]);

        $bedrock = ProjectType::create([
            'name' => 'Bedrock',
            'icon' => 'fab-wordpress',
            'description' => 'Site sous CMS WordPress avec le boilerplate (modèle) Bedrock qui permet de gérer WordPress avec Composer et Git et de façon plus sécurisé.',
            'is_default' => true,
        ]);

        $laravel = ProjectType::create([
            'name' => 'Laravel',
            'icon' => 'fab-laravel',
            'description' => 'Application métier sur mesure performante',
            'is_default' => false,
        ]);

        $php = ProjectType::create([
            'name' => 'PHP / Symfony',
            'icon' => 'fab-php',
            'description' => 'Développement spécifique PHP ou framework Symfony',
            'is_default' => false,
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
        // ── Génériques (toutes technos) ──
        Block::create(['name' => 'Header / Navigation', 'description' => 'Menu principal responsive avec logo, liens et burger mobile', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 3, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Footer', 'description' => 'Pied de page avec liens, infos légales et réseaux sociaux', 'type_unit' => 'hour', 'price_programming' => 0.5, 'price_integration' => 2, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Section Hero / Bannière', 'description' => 'Bannière d\'accueil avec titre, sous-titre, CTA et image de fond', 'type_unit' => 'hour', 'price_programming' => 0.5, 'price_integration' => 2, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Page de contenu standard', 'description' => 'Page simple avec texte, titres et images', 'type_unit' => 'hour', 'price_programming' => 0, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 1]);
        Block::create(['name' => 'Section À propos / Notre histoire', 'description' => 'Présentation de l\'entreprise avec texte, image et valeurs', 'type_unit' => 'hour', 'price_programming' => 0, 'price_integration' => 2, 'price_field_creation' => 0.5, 'price_content_management' => 1]);
        Block::create(['name' => 'Section Services / Offres', 'description' => 'Grille ou liste de services avec icônes, titres et descriptions', 'type_unit' => 'hour', 'price_programming' => 0.5, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 1]);
        Block::create(['name' => 'Section Équipe', 'description' => 'Cartes membres avec photo, nom, poste et liens sociaux', 'type_unit' => 'hour', 'price_programming' => 0.5, 'price_integration' => 2, 'price_field_creation' => 0.5, 'price_content_management' => 1]);
        Block::create(['name' => 'Section Témoignages / Avis clients', 'description' => 'Carousel ou grille de témoignages avec note et avatar', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 2, 'price_field_creation' => 0.5, 'price_content_management' => 1]);
        Block::create(['name' => 'Section Chiffres clés / Statistiques', 'description' => 'Compteurs animés avec icônes et labels', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 1.5, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Section FAQ', 'description' => 'Accordéon de questions/réponses', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 1.5, 'price_field_creation' => 0.5, 'price_content_management' => 1]);
        Block::create(['name' => 'Section Tarifs / Pricing', 'description' => 'Tableau comparatif des plans ou offres avec CTA', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 1]);
        Block::create(['name' => 'Section Partenaires / Logos clients', 'description' => 'Bande défilante ou grille de logos', 'type_unit' => 'hour', 'price_programming' => 0.5, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Galerie d\'images / Portfolio', 'description' => 'Grille filtrable avec lightbox', 'type_unit' => 'hour', 'price_programming' => 2, 'price_integration' => 2, 'price_field_creation' => 0.5, 'price_content_management' => 1]);
        Block::create(['name' => 'Slider / Carousel', 'description' => 'Diaporama d\'images ou de contenus avec navigation', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 2, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Formulaire de contact', 'description' => 'Formulaire avec validation, protection anti-spam et envoi email', 'type_unit' => 'hour', 'price_programming' => 2, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Carte Google Maps intégrée', 'description' => 'Carte interactive avec marqueur et infos de localisation', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Blog / Actualités', 'description' => 'Listing paginé et page détail d\'articles', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 3, 'price_field_creation' => 2, 'price_content_management' => 2]);
        Block::create(['name' => 'Newsletter / Inscription email', 'description' => 'Formulaire d\'abonnement connecté à Mailchimp, Brevo ou équivalent', 'type_unit' => 'hour', 'price_programming' => 2, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Popup / Bandeau promotionnel', 'description' => 'Modale ou bandeau avec message et CTA, déclenchement paramétrable', 'type_unit' => 'hour', 'price_programming' => 1.5, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Politique de confidentialité / Mentions légales', 'description' => 'Page texte légale avec ancres et mise en forme', 'type_unit' => 'hour', 'price_programming' => 0, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 2]);
        Block::create(['name' => 'Bandeau cookies (RGPD)', 'description' => 'Bannière de consentement conforme RGPD avec gestion des préférences', 'type_unit' => 'hour', 'price_programming' => 2, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Page 404 personnalisée', 'description' => 'Page d\'erreur avec message et redirection', 'type_unit' => 'hour', 'price_programming' => 0.5, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Intégration chat en direct', 'description' => 'Intégration Intercom, Tidio, Crisp ou équivalent', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0]);
        Block::create(['name' => 'Tableau / Liste de données', 'description' => 'Affichage tabulaire avec tri, filtre et pagination', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 1]);
        Block::create(['name' => 'Espace membre / Connexion', 'description' => 'Inscription, connexion, mot de passe oublié et profil de base', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Moteur de recherche interne', 'description' => 'Barre de recherche avec résultats filtrés et surlignage', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 2, 'price_field_creation' => 0, 'price_content_management' => 0]);
        Block::create(['name' => 'Timeline / Historique', 'description' => 'Frise chronologique verticale ou horizontale', 'type_unit' => 'hour', 'price_programming' => 0.5, 'price_integration' => 2, 'price_field_creation' => 0.5, 'price_content_management' => 1]);
        Block::create(['name' => 'Section Vidéo / Embed YouTube', 'description' => 'Lecteur vidéo ou iframe YouTube/Vimeo responsive', 'type_unit' => 'hour', 'price_programming' => 0.5, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0.5]);
        Block::create(['name' => 'Intégration Google Analytics / GTM', 'description' => 'Installation et configuration du tracking et du tag manager', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0]);

        // ── WordPress ──
        Block::create(['name' => 'Formulaire de contact (WP)', 'description' => 'Formulaire avec Contact Form 7 ou WPForms', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0.5, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Installation WooCommerce', 'description' => 'Installation et configuration de base e-commerce', 'type_unit' => 'fixed', 'price_programming' => 500, 'price_integration' => 200, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Fiche produit WooCommerce', 'description' => 'Template de fiche produit avec galerie, variantes et ajout au panier', 'type_unit' => 'hour', 'price_programming' => 2, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Listing produits WooCommerce', 'description' => 'Grille de produits avec filtres catégories et pagination', 'type_unit' => 'hour', 'price_programming' => 2, 'price_integration' => 2, 'price_field_creation' => 0.5, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Custom Post Type (CPT)', 'description' => 'Création d\'un type de contenu personnalisé avec ACF et template', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Page Builder (Elementor / Gutenberg)', 'description' => 'Configuration des sections et des blocs réutilisables', 'type_unit' => 'hour', 'price_programming' => 0.5, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Migration / Import de contenu WP', 'description' => 'Import XML, CSV ou migration base de données', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 2, 'price_content_management' => 2, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'SEO On-Page (Yoast / RankMath)', 'description' => 'Configuration plugin SEO, sitemap, balises meta et redirections', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Multilingue WPML / Polylang', 'description' => 'Configuration de la gestion multilingue et traduction du thème', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 2, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Header / Menu (WP)', 'description' => 'Menu principal avec Walker custom, mega-menu ou off-canvas mobile', 'type_unit' => 'hour', 'price_programming' => 2, 'price_integration' => 3, 'price_field_creation' => 0.5, 'price_content_management' => 0.5, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Page d\'accueil sur mesure (WP)', 'description' => 'Intégration d\'une homepage custom avec sections multiples', 'type_unit' => 'hour', 'price_programming' => 1, 'price_integration' => 5, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Panier & Tunnel de commande WooCommerce', 'description' => 'Template custom du panier, checkout et confirmation de commande', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 3, 'price_field_creation' => 0.5, 'price_content_management' => 0.5, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Système de réservation (WP)', 'description' => 'Plugin de réservation Amelia, BookingPress ou développement custom', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Optimisation performance WP', 'description' => 'Cache, minification, lazy-load, CDN et optimisation base de données', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 2, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Tableau d\'affichage / CPT Archive (WP)', 'description' => 'Page d\'archive custom avec filtres Ajax et pagination', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Espace membre (WP)', 'description' => 'Contenu restreint avec MemberPress ou code custom', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0.5, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Intégration ACF flexible content (WP)', 'description' => 'Système de blocs flexible avec Advanced Custom Fields Pro', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 3, 'price_field_creation' => 2, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Carrière / Offres d\'emploi (WP)', 'description' => 'CPT offres d\'emploi avec formulaire de candidature et filtre', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Génération de PDF (WP)', 'description' => 'Export PDF depuis un article ou un CPT via WP-DOMPDF ou plugin', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0, 'project_type_id' => $wp->id]);
        Block::create(['name' => 'Paiement WooCommerce (Stripe / PayPal)', 'description' => 'Configuration passerelles de paiement et webhooks', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $wp->id]);

        // ── Bedrock ──
        Block::create(['name' => 'Thème enfant sur mesure (Bedrock)', 'description' => 'Développement d\'un thème enfant avec Sage ou structure custom', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Plugin WordPress custom (Bedrock)', 'description' => 'Développement d\'une fonctionnalité métier en plugin dédié', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 2, 'price_field_creation' => 2, 'price_content_management' => 1, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Déploiement CI/CD (Bedrock)', 'description' => 'Pipeline de déploiement via GitHub Actions ou Buddy', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 2, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Thème Sage (Roots)', 'description' => 'Thème complet avec Sage, Tailwind et Blade templates', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 4, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'WooCommerce sur Bedrock', 'description' => 'Installation et personnalisation WooCommerce dans environnement Bedrock', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'ACF Blocks (Bedrock)', 'description' => 'Création de blocs Gutenberg custom via ACF Pro', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Configuration environnements (Bedrock)', 'description' => 'Gestion des environnements dev/staging/prod avec .env', 'type_unit' => 'hour', 'price_programming' => 2, 'price_integration' => 2, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Sécurité & durcissement WP (Bedrock)', 'description' => 'Déplacement wp-admin, HTTPS forcé, en-têtes de sécurité, 2FA', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Tests PHPUnit / WP (Bedrock)', 'description' => 'Mise en place tests unitaires et d\'intégration pour thème ou plugin', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Cron jobs & tâches planifiées WP', 'description' => 'WP-Cron custom ou cron système pour traitements récurrents', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Intégration API tierce (WP/Bedrock)', 'description' => 'Connexion à une API externe (CRM, ERP, SaaS) depuis WordPress', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Multisite WordPress (Bedrock)', 'description' => 'Configuration réseau multisite avec gestion des sous-domaines', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Optimisation base de données WP', 'description' => 'Nettoyage, indexation et requêtes optimisées pour WP Query', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'REST API WordPress custom', 'description' => 'Endpoints WP REST API sur mesure avec authentification JWT', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Headless WordPress (WP + Next.js)', 'description' => 'Architecture découplée avec WP comme backend GraphQL et front Next.js', 'type_unit' => 'hour', 'price_programming' => 10, 'price_integration' => 4, 'price_field_creation' => 2, 'price_content_management' => 1, 'project_type_id' => $bedrock->id]);
        Block::create(['name' => 'Formulaire avancé (WP/Bedrock)', 'description' => 'Formulaire multistep avec logique conditionnelle et sauvegarde en base', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0.5, 'project_type_id' => $bedrock->id]);

        // ── Laravel ──
        Block::create(['name' => 'Authentification & Profil (Laravel)', 'description' => 'Système complet avec Breeze ou Jetstream', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Tableau de bord admin (Filament)', 'description' => 'Interface de gestion CRUD complète', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 2, 'price_field_creation' => 3, 'price_content_management' => 2, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'API REST (Laravel)', 'description' => 'Endpoints CRUD avec authentification Sanctum et ressources Eloquent', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 1, 'price_field_creation' => 2, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Gestion des rôles & permissions (Spatie)', 'description' => 'Système de rôles utilisateurs avec spatie/laravel-permission', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Gestion de fichiers / Upload (Laravel)', 'description' => 'Upload avec validation, redimensionnement et stockage S3 ou local', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0.5, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Paiement Stripe (Laravel)', 'description' => 'Intégration Stripe Checkout ou Cashier pour abonnements et paiements', 'type_unit' => 'hour', 'price_programming' => 8, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Notifications email / queue (Laravel)', 'description' => 'Système de notifications avec templates mail et jobs en file d\'attente', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Import / Export CSV & Excel', 'description' => 'Import/export de données avec Laravel Excel', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Génération de PDF (Laravel)', 'description' => 'Génération de documents PDF depuis une vue Blade (DomPDF ou Browsershot)', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Composant Livewire dynamique', 'description' => 'Composant interactif temps réel sans rechargement de page', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0.5, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Déploiement & hébergement (Laravel)', 'description' => 'Configuration serveur, CI/CD, SSL, variables d\'environnement', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 2, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Abonnements SaaS (Laravel Cashier)', 'description' => 'Gestion des plans, essais, upgrades et webhooks Stripe/Paddle', 'type_unit' => 'hour', 'price_programming' => 8, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Multi-tenancy (Laravel)', 'description' => 'Architecture multi-tenant avec isolation des données par organisation', 'type_unit' => 'hour', 'price_programming' => 12, 'price_integration' => 2, 'price_field_creation' => 2, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Recherche avancée (Laravel Scout)', 'description' => 'Moteur de recherche full-text avec Meilisearch ou Algolia', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Cache & Optimisation (Laravel)', 'description' => 'Stratégie de cache Redis, query optimization et eager loading', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Tests Feature & Unit (Laravel)', 'description' => 'Suite de tests PHPUnit / Pest avec factories et mocks', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'WebSockets / Broadcasting (Laravel)', 'description' => 'Temps réel avec Reverb ou Pusher, Livewire Events', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Internationalisation i18n (Laravel)', 'description' => 'Traduction de l\'interface, détection locale et URLs localisées', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 2, 'price_field_creation' => 0, 'price_content_management' => 3, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Intégration API tierce (Laravel)', 'description' => 'Client HTTP Guzzle/Http facade pour connexion CRM, ERP ou SaaS', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Tableau de bord & graphiques (Laravel)', 'description' => 'Dashboard analytique avec Chart.js ou Recharts via Livewire', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 3, 'price_field_creation' => 1, 'price_content_management' => 0.5, 'project_type_id' => $laravel->id]);
        Block::create(['name' => 'Workflow / Machines à états (Laravel)', 'description' => 'Gestion d\'états métier avec spatie/laravel-model-states', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $laravel->id]);

        // ── PHP / Symfony ──
        Block::create(['name' => 'Authentification Symfony Security', 'description' => 'Système de sécurité avec providers, firewalls et voters', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0.5, 'project_type_id' => $php->id]);
        Block::create(['name' => 'CRUD Doctrine / EasyAdmin', 'description' => 'Entité Doctrine avec migrations et interface d\'administration EasyAdmin', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 2, 'price_field_creation' => 2, 'price_content_management' => 1, 'project_type_id' => $php->id]);
        Block::create(['name' => 'API Platform (Symfony)', 'description' => 'Endpoints REST/GraphQL auto-générés avec API Platform', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 1, 'price_field_creation' => 2, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Messenger / File de messages (Symfony)', 'description' => 'Traitement asynchrone avec Symfony Messenger et RabbitMQ ou Redis', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Formulaires Symfony Form', 'description' => 'Formulaires typés avec validations, types custom et rendu Twig', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0.5, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Gestion des rôles Symfony (Voters)', 'description' => 'Système d\'autorisation granulaire avec Voters et hiérarchie de rôles', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Emails transactionnels (Symfony Mailer)', 'description' => 'Templates Twig + Inky, envoi via SMTP/SES/Mailgun', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 1, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Upload & gestion des médias (Symfony)', 'description' => 'Upload avec VichUploader, redimensionnement Imagine et stockage Flysystem', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 1, 'price_field_creation' => 0.5, 'price_content_management' => 0.5, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Paiement Stripe (Symfony)', 'description' => 'Intégration Stripe SDK avec webhooks, remboursements et facturation', 'type_unit' => 'hour', 'price_programming' => 8, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Tests Symfony (PHPUnit + Panther)', 'description' => 'Tests unitaires, fonctionnels et E2E avec WebTestCase et Panther', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Cache Symfony (Redis / Memcached)', 'description' => 'Mise en cache des requêtes et fragments avec Cache Component', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Recherche Elasticsearch (Symfony)', 'description' => 'Indexation et recherche full-text avec FOS Elastica ou Ruflin', 'type_unit' => 'hour', 'price_programming' => 6, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Commandes Console Symfony', 'description' => 'Commandes CLI custom avec progress bar, scheduling et gestion des erreurs', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Internationalisation i18n (Symfony)', 'description' => 'Traductions avec Translator, URLs localisées et détection de locale', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 2, 'price_field_creation' => 0, 'price_content_management' => 3, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Déploiement Symfony (CI/CD)', 'description' => 'Pipeline GitHub Actions, Deployer, configuration Nginx/PHP-FPM', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 2, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Système de logs & monitoring (Symfony)', 'description' => 'Monolog avancé, intégration Sentry ou Datadog', 'type_unit' => 'hour', 'price_programming' => 3, 'price_integration' => 1, 'price_field_creation' => 0, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Multi-tenancy (Symfony)', 'description' => 'Architecture multi-tenant avec schemas séparés ou discriminateur', 'type_unit' => 'hour', 'price_programming' => 10, 'price_integration' => 2, 'price_field_creation' => 2, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'Import / Export données (Symfony)', 'description' => 'Import CSV/Excel avec EasyCSV ou Port et export en arrière-plan', 'type_unit' => 'hour', 'price_programming' => 4, 'price_integration' => 1, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $php->id]);
        Block::create(['name' => 'WebSockets (Symfony Mercure)', 'description' => 'Temps réel avec Mercure Hub et Server-Sent Events', 'type_unit' => 'hour', 'price_programming' => 5, 'price_integration' => 2, 'price_field_creation' => 1, 'price_content_management' => 0, 'project_type_id' => $php->id]);

        // 4. Add-ons (Options)
        Option::create([
            'name' => 'Optimisation SEO avancée',
            'type' => 'fixed_hours',
            'value' => 5,
            'description' => 'Recherche mots-clés et optimisation technique',
            'project_type_id' => $wp->id,
        ]);

        Option::create([
            'name' => 'Formation administrateur',
            'type' => 'fixed_price',
            'value' => 450,
            'description' => 'Demi-journée de formation (distanciel)',
            'project_type_id' => null,
        ]);

        Option::create([
            'name' => 'Maintenance annuelle',
            'type' => 'percentage',
            'value' => 15,
            'calculation_base' => 'global',
            'description' => '15% du total du devis',
            'project_type_id' => null,
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

        // Assigner les données de base à Jael
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
