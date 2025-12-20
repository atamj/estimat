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
        Schema::table('setups', function (Blueprint $table) {
            $table->renameColumn('amount', 'fixed_price');
            $table->float('fixed_hours')->default(0);
            $table->foreignId('project_type_id')->nullable()->constrained()->onDelete('set null')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('setups', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn(['project_type_id', 'fixed_hours']);
            $table->renameColumn('fixed_price', 'amount');
        });
    }
};
