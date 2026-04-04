<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('block_price_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 10); // 'HOUR' or currency code (EUR, USD...)
            $table->decimal('price_programming', 10, 2)->default(0);
            $table->decimal('price_integration', 10, 2)->default(0);
            $table->decimal('price_field_creation', 10, 2)->default(0);
            $table->decimal('price_content_management', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['block_id', 'currency']);
        });

        // Migrate existing block data
        DB::table('blocks')->orderBy('id')->each(function ($block) {
            $currency = $block->type_unit === 'hour' ? 'HOUR' : 'EUR';
            DB::table('block_price_sets')->insert([
                'block_id' => $block->id,
                'currency' => $currency,
                'price_programming' => $block->price_programming,
                'price_integration' => $block->price_integration,
                'price_field_creation' => $block->price_field_creation,
                'price_content_management' => $block->price_content_management,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->dropColumn(['type_unit', 'price_programming', 'price_integration', 'price_field_creation', 'price_content_management']);
        });
    }

    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->string('type_unit')->default('hour')->after('description');
            $table->decimal('price_programming', 10, 2)->default(0);
            $table->decimal('price_integration', 10, 2)->default(0);
            $table->decimal('price_field_creation', 10, 2)->default(0);
            $table->decimal('price_content_management', 10, 2)->default(0);
        });

        Schema::dropIfExists('block_price_sets');
    }
};
