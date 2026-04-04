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
        Schema::create('template_page_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_page_id')->constrained()->onDelete('cascade');
            $table->foreignId('block_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->integer('order')->default(0);
            $table->decimal('price_programming', 10, 2)->nullable();
            $table->decimal('price_integration', 10, 2)->nullable();
            $table->decimal('price_field_creation', 10, 2)->nullable();
            $table->decimal('price_content_management', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_page_blocks');
    }
};
