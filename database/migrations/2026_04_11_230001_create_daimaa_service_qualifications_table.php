<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daimaa_service_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daimaa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_qualified')->default(true);
            $table->timestamps();
            $table->unique(['daimaa_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daimaa_service_qualifications');
    }
};
