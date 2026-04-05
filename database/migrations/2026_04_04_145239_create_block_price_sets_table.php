<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('block_price_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 10);
            $table->decimal('price_programming', 10, 2)->default(0);
            $table->decimal('price_integration', 10, 2)->default(0);
            $table->decimal('price_field_creation', 10, 2)->default(0);
            $table->decimal('price_content_management', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['block_id', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_price_sets');
    }
};
