<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\BlockPriceSet;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminBlockSeeder extends Seeder
{
    private int $catalogOwnerUserId;

    public function run(): void
    {
        $this->catalogOwnerUserId = User::query()
            ->where('email', AdminUserSeeder::ADMIN_EMAIL)
            ->firstOrFail()
            ->id;

        $wp = ProjectType::where('name', 'WordPress')->firstOrFail();
        $bedrock = ProjectType::where('name', 'Bedrock')->firstOrFail();
        $laravel = ProjectType::where('name', 'Laravel')->firstOrFail();
        $php = ProjectType::where('name', 'PHP / Symfony')->firstOrFail();

        // ── Génériques (toutes technos) ──
        $this->seedBlock('Header / Navigation', 'Menu principal responsive avec logo, liens et burger mobile', null, 'hour', 1, 3, 0.5, 0.5);
        $this->seedBlock('Footer', 'Pied de page avec liens, infos légales et réseaux sociaux', null, 'hour', 0.5, 2, 0.5, 0.5);
        $this->seedBlock('Section Hero / Bannière', 'Bannière d\'accueil avec titre, sous-titre, CTA et image de fond', null, 'hour', 0.5, 2, 0.5, 0.5);
        $this->seedBlock('Page de contenu standard', 'Page simple avec texte, titres et images', null, 'hour', 0, 2, 1, 1);
        $this->seedBlock('Section À propos / Notre histoire', 'Présentation de l\'entreprise avec texte, image et valeurs', null, 'hour', 0, 2, 0.5, 1);
        $this->seedBlock('Section Services / Offres', 'Grille ou liste de services avec icônes, titres et descriptions', null, 'hour', 0.5, 2, 1, 1);
        $this->seedBlock('Section Équipe', 'Cartes membres avec photo, nom, poste et liens sociaux', null, 'hour', 0.5, 2, 0.5, 1);
        $this->seedBlock('Section Témoignages / Avis clients', 'Carousel ou grille de témoignages avec note et avatar', null, 'hour', 1, 2, 0.5, 1);
        $this->seedBlock('Section Chiffres clés / Statistiques', 'Compteurs animés avec icônes et labels', null, 'hour', 1, 1.5, 0.5, 0.5);
        $this->seedBlock('Section FAQ', 'Accordéon de questions/réponses', null, 'hour', 1, 1.5, 0.5, 1);
        $this->seedBlock('Section Tarifs / Pricing', 'Tableau comparatif des plans ou offres avec CTA', null, 'hour', 1, 3, 1, 1);
        $this->seedBlock('Section Partenaires / Logos clients', 'Bande défilante ou grille de logos', null, 'hour', 0.5, 1, 0.5, 0.5);
        $this->seedBlock('Galerie d\'images / Portfolio', 'Grille filtrable avec lightbox', null, 'hour', 2, 2, 0.5, 1);
        $this->seedBlock('Slider / Carousel', 'Diaporama d\'images ou de contenus avec navigation', null, 'hour', 1, 2, 0.5, 0.5);
        $this->seedBlock('Formulaire de contact', 'Formulaire avec validation, protection anti-spam et envoi email', null, 'hour', 2, 1, 0.5, 0.5);
        $this->seedBlock('Carte Google Maps intégrée', 'Carte interactive avec marqueur et infos de localisation', null, 'hour', 1, 1, 0.5, 0.5);
        $this->seedBlock('Blog / Actualités', 'Listing paginé et page détail d\'articles', null, 'hour', 3, 3, 2, 2);
        $this->seedBlock('Newsletter / Inscription email', 'Formulaire d\'abonnement connecté à Mailchimp, Brevo ou équivalent', null, 'hour', 2, 1, 0.5, 0.5);
        $this->seedBlock('Popup / Bandeau promotionnel', 'Modale ou bandeau avec message et CTA, déclenchement paramétrable', null, 'hour', 1.5, 1, 0.5, 0.5);
        $this->seedBlock('Politique de confidentialité / Mentions légales', 'Page texte légale avec ancres et mise en forme', null, 'hour', 0, 1, 0.5, 2);
        $this->seedBlock('Bandeau cookies (RGPD)', 'Bannière de consentement conforme RGPD avec gestion des préférences', null, 'hour', 2, 1, 0, 0.5);
        $this->seedBlock('Page 404 personnalisée', 'Page d\'erreur avec message et redirection', null, 'hour', 0.5, 1, 0, 0.5);
        $this->seedBlock('Intégration chat en direct', 'Intégration Intercom, Tidio, Crisp ou équivalent', null, 'hour', 1, 1, 0, 0);
        $this->seedBlock('Tableau / Liste de données', 'Affichage tabulaire avec tri, filtre et pagination', null, 'hour', 3, 2, 1, 1);
        $this->seedBlock('Espace membre / Connexion', 'Inscription, connexion, mot de passe oublié et profil de base', null, 'hour', 5, 2, 1, 0.5);
        $this->seedBlock('Moteur de recherche interne', 'Barre de recherche avec résultats filtrés et surlignage', null, 'hour', 4, 2, 0, 0);
        $this->seedBlock('Timeline / Historique', 'Frise chronologique verticale ou horizontale', null, 'hour', 0.5, 2, 0.5, 1);
        $this->seedBlock('Section Vidéo / Embed YouTube', 'Lecteur vidéo ou iframe YouTube/Vimeo responsive', null, 'hour', 0.5, 1, 0.5, 0.5);
        $this->seedBlock('Intégration Google Analytics / GTM', 'Installation et configuration du tracking et du tag manager', null, 'hour', 1, 1, 0, 0);

        // ── WordPress ──
        $this->seedBlock('Formulaire de contact (WP)', 'Formulaire avec Contact Form 7 ou WPForms', $wp->id, 'hour', 1, 1, 0.5, 0.5);
        $this->seedBlock('Installation WooCommerce', 'Installation et configuration de base e-commerce', $wp->id, 'fixed', 500, 200, 0, 0);
        $this->seedBlock('Fiche produit WooCommerce', 'Template de fiche produit avec galerie, variantes et ajout au panier', $wp->id, 'hour', 2, 3, 1, 1);
        $this->seedBlock('Listing produits WooCommerce', 'Grille de produits avec filtres catégories et pagination', $wp->id, 'hour', 2, 2, 0.5, 1);
        $this->seedBlock('Custom Post Type (CPT)', 'Création d\'un type de contenu personnalisé avec ACF et template', $wp->id, 'hour', 3, 2, 1, 1);
        $this->seedBlock('Page Builder (Elementor / Gutenberg)', 'Configuration des sections et des blocs réutilisables', $wp->id, 'hour', 0.5, 3, 1, 1);
        $this->seedBlock('Migration / Import de contenu WP', 'Import XML, CSV ou migration base de données', $wp->id, 'hour', 3, 1, 2, 2);
        $this->seedBlock('SEO On-Page (Yoast / RankMath)', 'Configuration plugin SEO, sitemap, balises meta et redirections', $wp->id, 'hour', 1, 1, 0, 1);
        $this->seedBlock('Multilingue WPML / Polylang', 'Configuration de la gestion multilingue et traduction du thème', $wp->id, 'hour', 3, 2, 1, 2);
        $this->seedBlock('Header / Menu (WP)', 'Menu principal avec Walker custom, mega-menu ou off-canvas mobile', $wp->id, 'hour', 2, 3, 0.5, 0.5);
        $this->seedBlock('Page d\'accueil sur mesure (WP)', 'Intégration d\'une homepage custom avec sections multiples', $wp->id, 'hour', 1, 5, 1, 1);
        $this->seedBlock('Panier & Tunnel de commande WooCommerce', 'Template custom du panier, checkout et confirmation de commande', $wp->id, 'hour', 3, 3, 0.5, 0.5);
        $this->seedBlock('Système de réservation (WP)', 'Plugin de réservation Amelia, BookingPress ou développement custom', $wp->id, 'hour', 4, 3, 1, 1);
        $this->seedBlock('Optimisation performance WP', 'Cache, minification, lazy-load, CDN et optimisation base de données', $wp->id, 'hour', 3, 2, 0, 0);
        $this->seedBlock('Tableau d\'affichage / CPT Archive (WP)', 'Page d\'archive custom avec filtres Ajax et pagination', $wp->id, 'hour', 3, 3, 1, 1);
        $this->seedBlock('Espace membre (WP)', 'Contenu restreint avec MemberPress ou code custom', $wp->id, 'hour', 5, 2, 1, 0.5);
        $this->seedBlock('Intégration ACF flexible content (WP)', 'Système de blocs flexible avec Advanced Custom Fields Pro', $wp->id, 'hour', 3, 3, 2, 1);
        $this->seedBlock('Carrière / Offres d\'emploi (WP)', 'CPT offres d\'emploi avec formulaire de candidature et filtre', $wp->id, 'hour', 3, 3, 1, 1);
        $this->seedBlock('Génération de PDF (WP)', 'Export PDF depuis un article ou un CPT via WP-DOMPDF ou plugin', $wp->id, 'hour', 3, 1, 0.5, 0);
        $this->seedBlock('Paiement WooCommerce (Stripe / PayPal)', 'Configuration passerelles de paiement et webhooks', $wp->id, 'hour', 3, 1, 0, 0);

        // ── Bedrock ──
        $this->seedBlock('Thème enfant sur mesure (Bedrock)', 'Développement d\'un thème enfant avec Sage ou structure custom', $bedrock->id, 'hour', 4, 3, 1, 1);
        $this->seedBlock('Plugin WordPress custom (Bedrock)', 'Développement d\'une fonctionnalité métier en plugin dédié', $bedrock->id, 'hour', 6, 2, 2, 1);
        $this->seedBlock('Déploiement CI/CD (Bedrock)', 'Pipeline de déploiement via GitHub Actions ou Buddy', $bedrock->id, 'hour', 4, 2, 0, 0);
        $this->seedBlock('Thème Sage (Roots)', 'Thème complet avec Sage, Tailwind et Blade templates', $bedrock->id, 'hour', 5, 4, 1, 1);
        $this->seedBlock('WooCommerce sur Bedrock', 'Installation et personnalisation WooCommerce dans environnement Bedrock', $bedrock->id, 'hour', 4, 3, 1, 1);
        $this->seedBlock('ACF Blocks (Bedrock)', 'Création de blocs Gutenberg custom via ACF Pro', $bedrock->id, 'hour', 3, 3, 1, 1);
        $this->seedBlock('Configuration environnements (Bedrock)', 'Gestion des environnements dev/staging/prod avec .env', $bedrock->id, 'hour', 2, 2, 0, 0);
        $this->seedBlock('Sécurité & durcissement WP (Bedrock)', 'Déplacement wp-admin, HTTPS forcé, en-têtes de sécurité, 2FA', $bedrock->id, 'hour', 3, 1, 0, 0);
        $this->seedBlock('Tests PHPUnit / WP (Bedrock)', 'Mise en place tests unitaires et d\'intégration pour thème ou plugin', $bedrock->id, 'hour', 5, 1, 0, 0);
        $this->seedBlock('Cron jobs & tâches planifiées WP', 'WP-Cron custom ou cron système pour traitements récurrents', $bedrock->id, 'hour', 3, 1, 0, 0);
        $this->seedBlock('Intégration API tierce (WP/Bedrock)', 'Connexion à une API externe (CRM, ERP, SaaS) depuis WordPress', $bedrock->id, 'hour', 6, 1, 1, 0);
        $this->seedBlock('Multisite WordPress (Bedrock)', 'Configuration réseau multisite avec gestion des sous-domaines', $bedrock->id, 'hour', 5, 2, 1, 1);
        $this->seedBlock('Optimisation base de données WP', 'Nettoyage, indexation et requêtes optimisées pour WP Query', $bedrock->id, 'hour', 3, 1, 0, 0);
        $this->seedBlock('REST API WordPress custom', 'Endpoints WP REST API sur mesure avec authentification JWT', $bedrock->id, 'hour', 5, 1, 1, 0);
        $this->seedBlock('Headless WordPress (WP + Next.js)', 'Architecture découplée avec WP comme backend GraphQL et front Next.js', $bedrock->id, 'hour', 10, 4, 2, 1);
        $this->seedBlock('Formulaire avancé (WP/Bedrock)', 'Formulaire multistep avec logique conditionnelle et sauvegarde en base', $bedrock->id, 'hour', 4, 2, 1, 0.5);

        // ── Laravel ──
        $this->seedBlock('Authentification & Profil (Laravel)', 'Système complet avec Breeze ou Jetstream', $laravel->id, 'hour', 4, 2, 1, 1);
        $this->seedBlock('Tableau de bord admin (Filament)', 'Interface de gestion CRUD complète', $laravel->id, 'hour', 6, 2, 3, 2);
        $this->seedBlock('API REST (Laravel)', 'Endpoints CRUD avec authentification Sanctum et ressources Eloquent', $laravel->id, 'hour', 6, 1, 2, 0);
        $this->seedBlock('Gestion des rôles & permissions (Spatie)', 'Système de rôles utilisateurs avec spatie/laravel-permission', $laravel->id, 'hour', 4, 1, 1, 0);
        $this->seedBlock('Gestion de fichiers / Upload (Laravel)', 'Upload avec validation, redimensionnement et stockage S3 ou local', $laravel->id, 'hour', 4, 1, 0.5, 0.5);
        $this->seedBlock('Paiement Stripe (Laravel)', 'Intégration Stripe Checkout ou Cashier pour abonnements et paiements', $laravel->id, 'hour', 8, 2, 1, 0);
        $this->seedBlock('Notifications email / queue (Laravel)', 'Système de notifications avec templates mail et jobs en file d\'attente', $laravel->id, 'hour', 4, 1, 1, 1);
        $this->seedBlock('Import / Export CSV & Excel', 'Import/export de données avec Laravel Excel', $laravel->id, 'hour', 4, 1, 1, 0);
        $this->seedBlock('Génération de PDF (Laravel)', 'Génération de documents PDF depuis une vue Blade (DomPDF ou Browsershot)', $laravel->id, 'hour', 3, 1, 0.5, 0);
        $this->seedBlock('Composant Livewire dynamique', 'Composant interactif temps réel sans rechargement de page', $laravel->id, 'hour', 4, 2, 1, 0.5);
        $this->seedBlock('Déploiement & hébergement (Laravel)', 'Configuration serveur, CI/CD, SSL, variables d\'environnement', $laravel->id, 'hour', 4, 2, 0, 0);
        $this->seedBlock('Abonnements SaaS (Laravel Cashier)', 'Gestion des plans, essais, upgrades et webhooks Stripe/Paddle', $laravel->id, 'hour', 8, 2, 1, 0);
        $this->seedBlock('Multi-tenancy (Laravel)', 'Architecture multi-tenant avec isolation des données par organisation', $laravel->id, 'hour', 12, 2, 2, 0);
        $this->seedBlock('Recherche avancée (Laravel Scout)', 'Moteur de recherche full-text avec Meilisearch ou Algolia', $laravel->id, 'hour', 5, 2, 1, 0);
        $this->seedBlock('Cache & Optimisation (Laravel)', 'Stratégie de cache Redis, query optimization et eager loading', $laravel->id, 'hour', 5, 1, 0, 0);
        $this->seedBlock('Tests Feature & Unit (Laravel)', 'Suite de tests PHPUnit / Pest avec factories et mocks', $laravel->id, 'hour', 6, 1, 0, 0);
        $this->seedBlock('WebSockets / Broadcasting (Laravel)', 'Temps réel avec Reverb ou Pusher, Livewire Events', $laravel->id, 'hour', 6, 2, 1, 0);
        $this->seedBlock('Internationalisation i18n (Laravel)', 'Traduction de l\'interface, détection locale et URLs localisées', $laravel->id, 'hour', 4, 2, 0, 3);
        $this->seedBlock('Intégration API tierce (Laravel)', 'Client HTTP Guzzle/Http facade pour connexion CRM, ERP ou SaaS', $laravel->id, 'hour', 5, 1, 1, 0);
        $this->seedBlock('Tableau de bord & graphiques (Laravel)', 'Dashboard analytique avec Chart.js ou Recharts via Livewire', $laravel->id, 'hour', 5, 3, 1, 0.5);
        $this->seedBlock('Workflow / Machines à états (Laravel)', 'Gestion d\'états métier avec spatie/laravel-model-states', $laravel->id, 'hour', 6, 1, 1, 0);

        // ── PHP / Symfony ──
        $this->seedBlock('Authentification Symfony Security', 'Système de sécurité avec providers, firewalls et voters', $php->id, 'hour', 5, 2, 1, 0.5);
        $this->seedBlock('CRUD Doctrine / EasyAdmin', 'Entité Doctrine avec migrations et interface d\'administration EasyAdmin', $php->id, 'hour', 5, 2, 2, 1);
        $this->seedBlock('API Platform (Symfony)', 'Endpoints REST/GraphQL auto-générés avec API Platform', $php->id, 'hour', 6, 1, 2, 0);
        $this->seedBlock('Messenger / File de messages (Symfony)', 'Traitement asynchrone avec Symfony Messenger et RabbitMQ ou Redis', $php->id, 'hour', 5, 1, 1, 0);
        $this->seedBlock('Formulaires Symfony Form', 'Formulaires typés avec validations, types custom et rendu Twig', $php->id, 'hour', 4, 2, 1, 0.5);
        $this->seedBlock('Gestion des rôles Symfony (Voters)', 'Système d\'autorisation granulaire avec Voters et hiérarchie de rôles', $php->id, 'hour', 4, 1, 1, 0);
        $this->seedBlock('Emails transactionnels (Symfony Mailer)', 'Templates Twig + Inky, envoi via SMTP/SES/Mailgun', $php->id, 'hour', 3, 1, 1, 1);
        $this->seedBlock('Upload & gestion des médias (Symfony)', 'Upload avec VichUploader, redimensionnement Imagine et stockage Flysystem', $php->id, 'hour', 4, 1, 0.5, 0.5);
        $this->seedBlock('Paiement Stripe (Symfony)', 'Intégration Stripe SDK avec webhooks, remboursements et facturation', $php->id, 'hour', 8, 2, 1, 0);
        $this->seedBlock('Tests Symfony (PHPUnit + Panther)', 'Tests unitaires, fonctionnels et E2E avec WebTestCase et Panther', $php->id, 'hour', 6, 1, 0, 0);
        $this->seedBlock('Cache Symfony (Redis / Memcached)', 'Mise en cache des requêtes et fragments avec Cache Component', $php->id, 'hour', 4, 1, 0, 0);
        $this->seedBlock('Recherche Elasticsearch (Symfony)', 'Indexation et recherche full-text avec FOS Elastica ou Ruflin', $php->id, 'hour', 6, 2, 1, 0);
        $this->seedBlock('Commandes Console Symfony', 'Commandes CLI custom avec progress bar, scheduling et gestion des erreurs', $php->id, 'hour', 3, 1, 0, 0);
        $this->seedBlock('Internationalisation i18n (Symfony)', 'Traductions avec Translator, URLs localisées et détection de locale', $php->id, 'hour', 4, 2, 0, 3);
        $this->seedBlock('Déploiement Symfony (CI/CD)', 'Pipeline GitHub Actions, Deployer, configuration Nginx/PHP-FPM', $php->id, 'hour', 5, 2, 0, 0);
        $this->seedBlock('Système de logs & monitoring (Symfony)', 'Monolog avancé, intégration Sentry ou Datadog', $php->id, 'hour', 3, 1, 0, 0);
        $this->seedBlock('Multi-tenancy (Symfony)', 'Architecture multi-tenant avec schemas séparés ou discriminateur', $php->id, 'hour', 10, 2, 2, 0);
        $this->seedBlock('Import / Export données (Symfony)', 'Import CSV/Excel avec EasyCSV ou Port et export en arrière-plan', $php->id, 'hour', 4, 1, 1, 0);
        $this->seedBlock('WebSockets (Symfony Mercure)', 'Temps réel avec Mercure Hub et Server-Sent Events', $php->id, 'hour', 5, 2, 1, 0);
    }

    /**
     * @param  'hour'|'fixed'  $pricingMode  hour → jeu de prix en devise HOUR ; fixed → montants en EUR (ex. WooCommerce forfait)
     */
    private function seedBlock(
        string $name,
        string $description,
        ?int $projectTypeId,
        string $pricingMode,
        float $priceProgramming,
        float $priceIntegration,
        float $priceFieldCreation,
        float $priceContentManagement,
    ): void {
        $block = Block::create([
            'user_id' => $this->catalogOwnerUserId,
            'name' => $name,
            'description' => $description,
            'project_type_id' => $projectTypeId,
        ]);

        $currency = $pricingMode === 'fixed' ? 'EUR' : 'HOUR';

        BlockPriceSet::create([
            'block_id' => $block->id,
            'currency' => $currency,
            'price_programming' => $priceProgramming,
            'price_integration' => $priceIntegration,
            'price_field_creation' => $priceFieldCreation,
            'price_content_management' => $priceContentManagement,
        ]);
    }
}
