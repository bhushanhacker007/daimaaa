<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daimaa_profiles', function (Blueprint $table) {
            // Personal details
            $table->date('date_of_birth')->nullable()->after('bio');
            $table->string('gender', 20)->nullable()->after('date_of_birth');
            $table->string('marital_status', 20)->nullable()->after('gender');
            $table->string('education')->nullable()->after('marital_status');
            $table->string('blood_group', 10)->nullable()->after('education');
            $table->json('languages_spoken')->nullable()->after('blood_group');
            $table->string('emergency_contact_name')->nullable()->after('languages_spoken');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');

            // Aadhaar KYC
            $table->text('aadhaar_number')->nullable()->after('emergency_contact_phone');
            $table->string('aadhaar_name')->nullable()->after('aadhaar_number');
            $table->timestamp('aadhaar_verified_at')->nullable()->after('aadhaar_name');

            // PAN KYC
            $table->text('pan_number')->nullable()->after('aadhaar_verified_at');
            $table->string('pan_name')->nullable()->after('pan_number');
            $table->timestamp('pan_verified_at')->nullable()->after('pan_name');

            // Bank details
            $table->text('bank_account_number')->nullable()->after('pan_verified_at');
            $table->string('bank_ifsc', 20)->nullable()->after('bank_account_number');
            $table->string('bank_name')->nullable()->after('bank_ifsc');
            $table->string('bank_account_holder')->nullable()->after('bank_name');
            $table->timestamp('bank_verified_at')->nullable()->after('bank_account_holder');
            $table->string('upi_id')->nullable()->after('bank_verified_at');

            // Cashfree integration
            $table->string('cashfree_beneficiary_id')->nullable()->after('upi_id');
        });
    }

    public function down(): void
    {
        Schema::table('daimaa_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth', 'gender', 'marital_status', 'education', 'blood_group',
                'languages_spoken', 'emergency_contact_name', 'emergency_contact_phone',
                'aadhaar_number', 'aadhaar_name', 'aadhaar_verified_at',
                'pan_number', 'pan_name', 'pan_verified_at',
                'bank_account_number', 'bank_ifsc', 'bank_name', 'bank_account_holder',
                'bank_verified_at', 'upi_id', 'cashfree_beneficiary_id',
            ]);
        });
    }
};
