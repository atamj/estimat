<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use App\Models\TranslationConfig;
use Illuminate\Database\Seeder;

class TranslationConfigSeeder extends Seeder
{
    public function run(): void
    {
        $wp = ProjectType::where('name', 'WordPress')->firstOrFail();
        $laravel = ProjectType::where('name', 'Laravel')->firstOrFail();

        TranslationConfig::create([
            'project_type_id' => $wp->id,
            'default_fixed_hours' => 3,
            'default_fixed_price' => 0,
            'default_percentage' => 15,
        ]);

        TranslationConfig::create([
            'project_type_id' => $laravel->id,
            'default_fixed_hours' => 5,
            'default_fixed_price' => 0,
            'default_percentage' => 10,
        ]);

        TranslationConfig::create([
            'project_type_id' => null,
            'default_fixed_hours' => 2,
            'default_fixed_price' => 150,
            'default_percentage' => 10,
        ]);
    }
}
