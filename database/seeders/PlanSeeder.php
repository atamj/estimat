<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
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

        Plan::create([
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

        Plan::create([
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
    }
}
