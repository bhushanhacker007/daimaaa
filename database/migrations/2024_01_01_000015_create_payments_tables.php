<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method')->default('manual'); // manual, online, upi, card
            $table->string('status')->default('pending'); // pending, success, failed, refunded
            $table->string('gateway_reference')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();
            $table->index(['booking_id', 'status']);
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained();
            $table->decimal('amount', 10, 2);
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, processed, rejected
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daimaa_id')->constrained('users');
            $table->decimal('amount', 10, 2);
            $table->string('period'); // e.g. "2024-01-W1"
            $table->string('status')->default('pending'); // pending, processed, failed
            $table->string('reference')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['daimaa_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payments');
    }
};
