<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('default_currency', 3)->default('EUR')->after('is_admin');
        });

        Schema::table('estimations', function (Blueprint $table) {
            $table->string('currency', 3)->default('EUR')->after('project_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('default_currency');
        });

        Schema::table('estimations', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
