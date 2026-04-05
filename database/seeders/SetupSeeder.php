<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use App\Models\Setup;
use App\Models\SetupPrice;
use Illuminate\Database\Seeder;

class SetupSeeder extends Seeder
{
    public function run(): void
    {
        $wp = ProjectType::where('name', 'WordPress')->firstOrFail();
        $bedrock = ProjectType::where('name', 'Bedrock')->firstOrFail();
        $laravel = ProjectType::where('name', 'Laravel')->firstOrFail();

        $this->createSetupWithOptionalPrice($bedrock->id, 'Installation & Config WP', 8, 0);
        $this->createSetupWithOptionalPrice($bedrock->id, 'Modifications projet interne', 0.5, 0);
        $this->createSetupWithOptionalPrice($bedrock->id, 'Modifications projet externe', 1.5, 0);

        $this->createSetupWithOptionalPrice($wp->id, 'Installation & Config WP', 12, 0);
        $this->createSetupWithOptionalPrice($wp->id, 'Modifications projet interne', 1.5, 0);
        $this->createSetupWithOptionalPrice($wp->id, 'Modifications projet externe', 2, 0);

        $this->createSetupWithOptionalPrice($laravel->id, 'Setup Laravel & DB', 8, 0);

        $this->createSetupWithOptionalPrice(null, 'Nouveau projet standard', 6, 300);
        $this->createSetupWithOptionalPrice(null, 'Reprise de projet externe', 12, 600);
    }

    private function createSetupWithOptionalPrice(?int $projectTypeId, string $type, float $fixedHours, float $fixedPriceEur): void
    {
        $setup = Setup::create([
            'project_type_id' => $projectTypeId,
            'type' => $type,
            'fixed_hours' => $fixedHours,
        ]);

        if ($fixedPriceEur > 0) {
            SetupPrice::create([
                'setup_id' => $setup->id,
                'currency' => 'EUR',
                'price' => $fixedPriceEur,
            ]);
        }
    }
}
