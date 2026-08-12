<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the foundational tables for Neo Kebuli.
     *
     * This migration was updated from the default Laravel scaffold to reflect
     * the actual production schema. The users table includes custom fields
     * (phone, is_active, last_login_at, soft_deletes) that were added during
     * initial development but whose migrations were not preserved in VCS.
     *
     * Also creates: roles, permissions, role_user, role_permission,
     * kbli_categories, kbli_sections, kbli_items, applications,
     * application_histories, application_documents, media,
     * activity_logs, news_categories, news.
     */
    public function up(): void
    {
        // ── Users ──────────────────────────────────────────────────────────
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Auth support ───────────────────────────────────────────────────
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });



        // ── KBLI Hierarchy ─────────────────────────────────────────────────
        Schema::create('kbli_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('kbli_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('code', 4);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('kbli_categories')->onDelete('cascade');
        });

        Schema::create('kbli_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('section_id');
            $table->string('code', 5)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('section_id')->references('id')->on('kbli_sections')->onDelete('cascade');
        });

        // ── Applications ───────────────────────────────────────────────────
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->unsignedBigInteger('kbli_item_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('product_name');
            $table->text('product_material');
            $table->text('production_process');
            $table->text('product_description')->nullable();
            $table->text('saker_address');
            $table->text('additional_information')->nullable();
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('kbli_item_id')->references('id')->on('kbli_items')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });

        // ── Application Histories ──────────────────────────────────────────
        Schema::create('application_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // ── Media / Documents ──────────────────────────────────────────────
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('path');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('checksum', 64)->nullable();
            $table->timestamps();
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
        });

        // ── Activity Logs ──────────────────────────────────────────────────
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // ── News ───────────────────────────────────────────────────────────
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('news_category_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('thumbnail')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->foreign('news_category_id')->references('id')->on('news_categories')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('media');
        Schema::dropIfExists('application_histories');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('kbli_items');
        Schema::dropIfExists('kbli_sections');
        Schema::dropIfExists('kbli_categories');

        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
