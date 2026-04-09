<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('daimaa_id')->nullable()->constrained('users');
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['daimaa_id', 'is_published']);
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable'); // user (daimaa KYC)
            $table->string('type'); // aadhaar, photo, certificate, pan
            $table->string('file_path');
            $table->string('original_name');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daimaa_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sun, 6=Sat
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->index(['daimaa_id', 'day_of_week']);
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->longText('answer');
            $table->string('category')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content');
            $table->string('city')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('avatar')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, updated, deleted, status_changed
            $table->nullableMorphs('auditable');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('availability_slots');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('reviews');
    }
};
