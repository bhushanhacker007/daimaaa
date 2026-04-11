<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_assignments', function (Blueprint $table) {
            $table->decimal('match_score', 5, 2)->nullable()->after('rejection_reason');
            $table->json('score_breakdown')->nullable()->after('match_score');
            $table->integer('dispatch_rank')->nullable()->after('score_breakdown');
            $table->timestamp('expires_at')->nullable()->after('dispatch_rank');
            $table->string('dispatch_status')->default('pending')->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('booking_assignments', function (Blueprint $table) {
            $table->dropColumn([
                'match_score', 'score_breakdown', 'dispatch_rank',
                'expires_at', 'dispatch_status',
            ]);
        });
    }
};
