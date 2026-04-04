<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setup_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setup_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 3);
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['setup_id', 'currency']);
        });

        // Migrate existing fixed_price data to setup_prices (EUR by default)
        DB::table('setups')->where('fixed_price', '>', 0)->orderBy('id')->each(function ($setup) {
            DB::table('setup_prices')->insert([
                'setup_id' => $setup->id,
                'currency' => 'EUR',
                'price' => $setup->fixed_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Schema::table('setups', function (Blueprint $table) {
            $table->dropColumn('fixed_price');
        });
    }

    public function down(): void
    {
        Schema::table('setups', function (Blueprint $table) {
            $table->decimal('fixed_price', 10, 2)->default(0)->after('fixed_hours');
        });

        Schema::dropIfExists('setup_prices');
    }
};
