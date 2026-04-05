<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
    }

    public function down(): void
    {
        Schema::dropIfExists('setup_prices');
    }
};
