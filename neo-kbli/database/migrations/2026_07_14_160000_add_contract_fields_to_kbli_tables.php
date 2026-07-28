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
        Schema::table('kbli_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('kbli_categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
            if (!Schema::hasColumn('kbli_categories', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });

        Schema::table('kbli_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('kbli_sections', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });

        Schema::table('kbli_items', function (Blueprint $table) {
            if (!Schema::hasColumn('kbli_items', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kbli_items', function (Blueprint $table) {
            if (Schema::hasColumn('kbli_items', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        Schema::table('kbli_sections', function (Blueprint $table) {
            if (Schema::hasColumn('kbli_sections', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        Schema::table('kbli_categories', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('kbli_categories', 'is_active')) $columns[] = 'is_active';
            if (Schema::hasColumn('kbli_categories', 'sort_order')) $columns[] = 'sort_order';
            
            if (count($columns) > 0) {
                $table->dropColumn($columns);
            }
        });
    }
};
