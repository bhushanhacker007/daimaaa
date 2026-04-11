<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_sessions', function (Blueprint $table) {
            $table->decimal('earning_amount', 10, 2)->nullable()->after('completed_at');
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->date('period_start')->nullable()->after('period');
            $table->date('period_end')->nullable()->after('period_start');
            $table->integer('sessions_count')->default(0)->after('period_end');
            $table->text('notes')->nullable()->after('reference');
        });
    }

    public function down(): void
    {
        Schema::table('booking_sessions', function (Blueprint $table) {
            $table->dropColumn('earning_amount');
        });

        Schema::table('payouts', function (Blueprint $table) {
            $table->dropColumn(['period_start', 'period_end', 'sessions_count', 'notes']);
        });
    }
};
