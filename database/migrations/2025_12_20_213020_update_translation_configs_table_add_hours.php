<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('translation_configs', function (Blueprint $table) {
            $table->decimal('default_fixed_hours', 10, 2)->default(0)->after('default_fixed_amount');
            $table->renameColumn('default_fixed_amount', 'default_fixed_price');
        });

        Schema::table('estimations', function (Blueprint $table) {
            $table->decimal('translation_fixed_hours', 10, 2)->default(0)->after('translation_fixed_amount');
            $table->renameColumn('translation_fixed_amount', 'translation_fixed_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translation_configs', function (Blueprint $table) {
            $table->renameColumn('default_fixed_price', 'default_fixed_amount');
            $table->dropColumn('default_fixed_hours');
        });

        Schema::table('estimations', function (Blueprint $table) {
            $table->renameColumn('translation_fixed_price', 'translation_fixed_amount');
            $table->dropColumn('translation_fixed_hours');
        });
    }
};
