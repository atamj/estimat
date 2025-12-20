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
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['fixed_price', 'fixed_hours', 'percentage'])->default('fixed_price');
            $table->decimal('value', 10, 2)->default(0);
            $table->enum('calculation_base', [
                'global',
                'blocks',
                'pages',
                'content',
                'content_fields'
            ])->nullable(); // Obligatoire si type = percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
