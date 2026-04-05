<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\ProjectType;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminOptionSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::query()->where('email', AdminUserSeeder::ADMIN_EMAIL)->firstOrFail()->id;

        $wp = ProjectType::where('name', 'WordPress')->firstOrFail();

        Option::create([
            'user_id' => $userId,
            'name' => 'Optimisation SEO avancée',
            'type' => 'fixed_hours',
            'value' => 5,
            'description' => 'Recherche mots-clés et optimisation technique',
            'project_type_id' => $wp->id,
        ]);

        Option::create([
            'user_id' => $userId,
            'name' => 'Formation administrateur',
            'type' => 'fixed_price',
            'value' => 450,
            'description' => 'Demi-journée de formation (distanciel)',
            'project_type_id' => null,
        ]);

        Option::create([
            'user_id' => $userId,
            'name' => 'Maintenance annuelle',
            'type' => 'percentage',
            'value' => 15,
            'calculation_base' => 'global',
            'description' => '15% du total du devis',
            'project_type_id' => null,
        ]);
    }
}
