<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('instant_available')->default(false)->after('hour_increment');
            $table->decimal('instant_surcharge', 10, 2)->default(0)->after('instant_available');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_instant')->default(false)->after('booked_hours');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['instant_available', 'instant_surcharge']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('is_instant');
        });
    }
};
