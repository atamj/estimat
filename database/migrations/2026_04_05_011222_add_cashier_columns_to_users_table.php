<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Columns already added by Cashier's create_customer_columns migration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
