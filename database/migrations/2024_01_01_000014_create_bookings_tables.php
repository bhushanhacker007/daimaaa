<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type'); // percent, fixed
            $table->decimal('value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('used_count')->default(0);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('package_id')->nullable()->constrained();
            $table->foreignId('service_id')->nullable()->constrained();
            $table->foreignId('address_id')->constrained();
            $table->foreignId('coupon_id')->nullable()->constrained();
            $table->string('status')->default('pending'); // pending, confirmed, assigned, in_progress, completed, cancelled, refunded
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'customer_id']);
            $table->index('scheduled_date');
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->morphs('itemable'); // service or add_on
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
        });

        Schema::create('booking_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daimaa_id')->nullable()->constrained('users');
            $table->integer('session_number');
            $table->timestamp('scheduled_at')->nullable();
            $table->string('status')->default('upcoming'); // upcoming, started, completed, cancelled, no_show
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['daimaa_id', 'status']);
        });

        Schema::create('booking_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daimaa_id')->constrained('users');
            $table->foreignId('assigned_by')->nullable()->constrained('users');
            $table->timestamp('assigned_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['coupon_id', 'user_id', 'booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('booking_status_histories');
        Schema::dropIfExists('booking_assignments');
        Schema::dropIfExists('booking_sessions');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('coupons');
    }
};
