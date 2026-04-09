<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('pincode', 10)->unique();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_serviceable')->default(true);
            $table->timestamps();
            $table->index('is_serviceable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincodes');
    }
};
