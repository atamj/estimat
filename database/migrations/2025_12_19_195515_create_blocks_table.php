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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type_unit', ['hour', 'fixed'])->default('hour');

            // Temps ou Prix selon type_unit
            $table->decimal('price_programming', 10, 2)->default(0);
            $table->decimal('price_integration', 10, 2)->default(0);
            $table->decimal('price_field_creation', 10, 2)->default(0);
            $table->decimal('price_content_management', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
