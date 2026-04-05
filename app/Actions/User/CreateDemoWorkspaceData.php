<?php

namespace App\Actions\User;

use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateDemoWorkspaceData
{
    use AsAction;

    public function handle(User $user): void
    {
        // 1. Project Type par défaut (Générique ou PHP)
        $phpType = \App\Models\ProjectType::create([
            'user_id' => $user->id,
            'name' => 'PHP / Sur-mesure',
            'icon' => 'fab-php',
            'description' => 'Projet PHP standard ou Framework sur-mesure',
            'is_default' => true,
        ]);

        // 2. Bases Techniques par défaut
        \App\Models\Setup::create([
            'user_id' => $user->id,
            'project_type_id' => $phpType->id,
            'type' => 'Installation & Configuration de base',
            'fixed_hours' => 4,
            'fixed_price' => 200,
        ]);

        \App\Models\Setup::create([
            'user_id' => $user->id,
            'project_type_id' => null,
            'type' => 'Maintenance initiale & Reprise',
            'fixed_hours' => 8,
            'fixed_price' => 400,
        ]);

        // 3. Blocs par défaut (5 blocs)
        $blocks = [
            [
                'name' => 'Navigation & Menu',
                'description' => 'Menu principal, liens de navigation et barre de recherche.',
                'type_unit' => 'hour',
                'price_programming' => 2,
                'price_integration' => 2,
                'price_field_creation' => 1,
                'price_content_management' => 0.5,
            ],
            [
                'name' => 'Logo & Identité',
                'description' => 'Intégration du logo, favicon et éléments d\'identité visuelle.',
                'type_unit' => 'hour',
                'price_programming' => 0.5,
                'price_integration' => 1,
                'price_field_creation' => 0.5,
                'price_content_management' => 0,
            ],
            [
                'name' => 'Copyright & Footer Simple',
                'description' => 'Mentions légales, copyright et liens bas de page.',
                'type_unit' => 'hour',
                'price_programming' => 0.5,
                'price_integration' => 1,
                'price_field_creation' => 0.5,
                'price_content_management' => 0.5,
            ],
            [
                'name' => 'Bannière d\'accueil (Hero)',
                'description' => 'Grande image ou vidéo d\'appel à l\'action en haut de page.',
                'type_unit' => 'hour',
                'price_programming' => 1,
                'price_integration' => 3,
                'price_field_creation' => 2,
                'price_content_management' => 1,
            ],
            [
                'name' => 'Duo Image & Texte (Alterné)',
                'description' => 'Section de contenu avec une image à gauche et du texte à droite (ou inversement).',
                'type_unit' => 'hour',
                'price_programming' => 1,
                'price_integration' => 2,
                'price_field_creation' => 2,
                'price_content_management' => 1.5,
            ],
        ];

        foreach ($blocks as $blockData) {
            $blockData['user_id'] = $user->id;
            \App\Models\Block::create($blockData);
        }

        // 4. Traduction par défaut
        \App\Models\TranslationConfig::create([
            'user_id' => $user->id,
            'default_fixed_price' => 150,
            'default_fixed_hours' => 2,
            'default_percentage' => 10,
        ]);

        // 5. Add-ons par défaut
        \App\Models\Option::create([
            'user_id' => $user->id,
            'name' => 'Optimisation SEO',
            'type' => 'fixed_price',
            'value' => 250,
            'description' => 'Configuration technique SEO de base.',
        ]);
    }
}
