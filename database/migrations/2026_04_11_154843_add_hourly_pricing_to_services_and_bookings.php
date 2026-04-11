<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price_per_hour', 10, 2)->nullable()->after('base_price');
            $table->decimal('min_hours', 4, 1)->default(1.0)->after('price_per_hour');
            $table->decimal('max_hours', 4, 1)->default(8.0)->after('min_hours');
            $table->decimal('hour_increment', 4, 1)->default(0.5)->after('max_hours');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('booked_hours', 4, 1)->nullable()->after('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('booked_hours');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['price_per_hour', 'min_hours', 'max_hours', 'hour_increment']);
        });
    }
};
