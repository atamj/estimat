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
        Schema::create('translation_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('default_fixed_price', 10, 2)->default(0);
            $table->decimal('default_fixed_hours', 10, 2)->default(0);
            $table->decimal('default_percentage', 5, 2)->default(0);
            $table->string('default_type')->default('fixed');
            $table->foreignId('project_type_id')->nullable()->constrained('project_types')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_configs');
    }
};
