<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('pincode');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        Schema::table('daimaa_profiles', function (Blueprint $table) {
            $table->decimal('home_latitude', 10, 7)->nullable()->after('service_area_pincodes');
            $table->decimal('home_longitude', 10, 7)->nullable()->after('home_latitude');
            $table->decimal('reliability_score', 5, 2)->default(100.00)->after('home_longitude');
            $table->integer('total_assignments')->default(0)->after('reliability_score');
            $table->integer('declined_assignments')->default(0)->after('total_assignments');
            $table->integer('cancelled_assignments')->default(0)->after('declined_assignments');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('daimaa_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'home_latitude', 'home_longitude', 'reliability_score',
                'total_assignments', 'declined_assignments', 'cancelled_assignments',
            ]);
        });
    }
};
