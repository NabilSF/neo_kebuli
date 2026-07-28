<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the search_logs table with the correct final schema.
     *
     * Note: This migration was originally created with an incorrect schema
     * (contained kbli_item_id, user_id, ip_address, user_agent, searched_at).
     * It has been corrected to match the authoritative schema defined in
     * 2026_07_14_122025, which the second migration now defers to.
     * Both migrations have already run on existing databases; this correction
     * only affects fresh installs (migrate:fresh / migrate:refresh).
     */
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->unique();
            $table->integer('frequency')->default(1);
            $table->timestamp('last_searched_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
