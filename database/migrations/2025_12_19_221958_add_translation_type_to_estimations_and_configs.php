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
        Schema::table('estimations', function (Blueprint $table) {
            $table->string('translation_type')->default('fixed')->after('translation_enabled');
        });

        Schema::table('translation_configs', function (Blueprint $table) {
            $table->string('default_type')->default('fixed')->after('default_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimations', function (Blueprint $table) {
            $table->dropColumn('translation_type');
        });

        Schema::table('translation_configs', function (Blueprint $table) {
            $table->dropColumn('default_type');
        });
    }
};
