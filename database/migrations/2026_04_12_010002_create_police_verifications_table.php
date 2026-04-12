<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('police_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daimaa_profile_id')->constrained('daimaa_profiles')->cascadeOnDelete();
            $table->string('status')->default('initiated'); // initiated, in_progress, cleared, failed, expired
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('cleared_at')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('agency_name')->nullable();
            $table->string('report_file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('police_verifications');
    }
};
