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
        Schema::table('blocks', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('options', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('translation_configs', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('estimations', function (Blueprint $table) {
            $table->foreignId('project_type_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('blocks', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn('project_type_id');
        });
        Schema::table('options', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn('project_type_id');
        });
        Schema::table('translation_configs', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn('project_type_id');
        });
        Schema::table('estimations', function (Blueprint $table) {
            $table->dropForeign(['project_type_id']);
            $table->dropColumn('project_type_id');
        });
    }
};
