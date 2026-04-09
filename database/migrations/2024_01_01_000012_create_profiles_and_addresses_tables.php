<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date_of_birth')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('daimaa_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('years_of_experience')->default(0);
            $table->text('bio')->nullable();
            $table->string('status')->default('pending'); // pending, verified, rejected, suspended
            $table->timestamp('verified_at')->nullable();
            $table->json('service_area_pincodes')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Home');
            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();
            $table->string('landmark')->nullable();
            $table->foreignId('city_id')->constrained();
            $table->string('pincode', 10);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('daimaa_profiles');
        Schema::dropIfExists('customer_profiles');
    }
};
