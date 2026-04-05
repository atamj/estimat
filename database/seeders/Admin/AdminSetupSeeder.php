<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use App\Models\Setup;
use App\Models\SetupPrice;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSetupSeeder extends Seeder
{
    public function run(): void
    {
        $ownerId = User::query()->where('email', AdminUserSeeder::ADMIN_EMAIL)->firstOrFail()->id;

        $wp = ProjectType::where('name', 'WordPress')->firstOrFail();
        $bedrock = ProjectType::where('name', 'Bedrock')->firstOrFail();
        $laravel = ProjectType::where('name', 'Laravel')->firstOrFail();

        $this->createSetupWithOptionalPrice($ownerId, $bedrock->id, 'Installation & Config WP', 8, 0);
        $this->createSetupWithOptionalPrice($ownerId, $bedrock->id, 'Modifications projet interne', 0.5, 0);
        $this->createSetupWithOptionalPrice($ownerId, $bedrock->id, 'Modifications projet externe', 1.5, 0);

        $this->createSetupWithOptionalPrice($ownerId, $wp->id, 'Installation & Config WP', 12, 0);
        $this->createSetupWithOptionalPrice($ownerId, $wp->id, 'Modifications projet interne', 1.5, 0);
        $this->createSetupWithOptionalPrice($ownerId, $wp->id, 'Modifications projet externe', 2, 0);

        $this->createSetupWithOptionalPrice($ownerId, $laravel->id, 'Setup Laravel & DB', 8, 0);

        $this->createSetupWithOptionalPrice($ownerId, null, 'Nouveau projet standard', 6, 300);
        $this->createSetupWithOptionalPrice($ownerId, null, 'Reprise de projet externe', 12, 600);
    }

    private function createSetupWithOptionalPrice(int $userId, ?int $projectTypeId, string $type, float $fixedHours, float $fixedPriceEur): void
    {
        $setup = Setup::create([
            'user_id' => $userId,
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
