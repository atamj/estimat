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
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('client_name');
            $table->string('project_name')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->enum('type', ['hour', 'fixed'])->default('hour');
            $table->foreignId('setup_id')->nullable()->constrained('setups');
            $table->foreignId('project_type_id')->nullable()->constrained('project_types')->nullOnDelete();
            $table->string('currency', 3)->default('EUR');

            $table->boolean('translation_enabled')->default(false);
            $table->string('translation_type')->default('fixed');
            $table->decimal('translation_fixed_price', 10, 2)->nullable();
            $table->decimal('translation_fixed_hours', 10, 2)->default(0);
            $table->decimal('translation_percentage', 5, 2)->nullable();
            $table->integer('translation_languages_count')->default(1);

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
