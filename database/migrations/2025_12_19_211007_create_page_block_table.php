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
        Schema::create('page_block', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->foreignId('block_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);

            // Surcharges (overrides)
            $table->decimal('price_programming', 10, 2)->nullable();
            $table->decimal('price_integration', 10, 2)->nullable();
            $table->decimal('price_field_creation', 10, 2)->nullable();
            $table->decimal('price_content_management', 10, 2)->nullable();

            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_block');
    }
};
