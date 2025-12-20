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
        Schema::create('estimations', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('project_name')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->enum('type', ['hour', 'fixed'])->default('hour');
            $table->foreignId('setup_id')->nullable()->constrained('setups');

            // Traduction
            $table->boolean('translation_enabled')->default(false);
            $table->decimal('translation_fixed_amount', 10, 2)->nullable();
            $table->decimal('translation_percentage', 5, 2)->nullable(); // Pourcentage sur contenu + champs

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimations');
    }
};
