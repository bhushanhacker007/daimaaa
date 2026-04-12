<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaimaaProfile extends Model
{
    protected $fillable = [
        'user_id', 'years_of_experience', 'bio', 'status', 'is_online', 'verified_at',
        'service_area_pincodes', 'home_latitude', 'home_longitude',
        'reliability_score', 'total_assignments', 'declined_assignments', 'cancelled_assignments',
        // Personal
        'date_of_birth', 'gender', 'marital_status', 'education', 'blood_group',
        'languages_spoken', 'emergency_contact_name', 'emergency_contact_phone',
        // Aadhaar
        'aadhaar_number', 'aadhaar_name', 'aadhaar_verified_at',
        // PAN
        'pan_number', 'pan_name', 'pan_verified_at',
        // Bank
        'bank_account_number', 'bank_ifsc', 'bank_name', 'bank_account_holder',
        'bank_verified_at', 'upi_id',
        // Cashfree
        'cashfree_beneficiary_id',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'is_online' => 'boolean',
            'service_area_pincodes' => 'array',
            'languages_spoken' => 'array',
            'home_latitude' => 'decimal:7',
            'home_longitude' => 'decimal:7',
            'reliability_score' => 'decimal:2',
            'date_of_birth' => 'date',
            'aadhaar_verified_at' => 'datetime',
            'pan_verified_at' => 'datetime',
            'bank_verified_at' => 'datetime',
            'aadhaar_number' => 'encrypted',
            'pan_number' => 'encrypted',
            'bank_account_number' => 'encrypted',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function policeVerifications()
    {
        return $this->hasMany(PoliceVerification::class);
    }

    public function latestPoliceVerification()
    {
        return $this->hasOne(PoliceVerification::class)->latestOfMany();
    }

    public function qualifiedServices()
    {
        return $this->hasManyThrough(
            Service::class,
            DaimaaServiceQualification::class,
            'daimaa_id',
            'id',
            'user_id',
            'service_id'
        )->where('daimaa_service_qualifications.is_qualified', true);
    }

    public function serviceQualifications()
    {
        return $this->hasMany(DaimaaServiceQualification::class, 'daimaa_id', 'user_id');
    }

    public function isQualifiedFor(int $serviceId): bool
    {
        return DaimaaServiceQualification::where('daimaa_id', $this->user_id)
            ->where('service_id', $serviceId)
            ->where('is_qualified', true)
            ->exists();
    }

    public function penalizeReliability(float $points): void
    {
        $this->update([
            'reliability_score' => max(0, (float) $this->reliability_score - $points),
        ]);
    }

    public function recordAssignment(bool $declined = false): void
    {
        $this->increment('total_assignments');
        if ($declined) {
            $this->increment('declined_assignments');
        }
    }

    // Masked display helpers

    public function maskedAadhaar(): string
    {
        $raw = $this->aadhaar_number;
        if (!$raw || strlen($raw) < 4) return '—';
        return 'XXXX-XXXX-' . substr($raw, -4);
    }

    public function maskedPan(): string
    {
        $raw = $this->pan_number;
        if (!$raw || strlen($raw) < 4) return '—';
        return str_repeat('X', strlen($raw) - 4) . substr($raw, -4);
    }

    public function maskedBankAccount(): string
    {
        $raw = $this->bank_account_number;
        if (!$raw || strlen($raw) < 4) return '—';
        return str_repeat('X', strlen($raw) - 4) . substr($raw, -4);
    }

    public function kycProgress(): int
    {
        $total = 5;
        $done = 0;
        if ($this->aadhaar_verified_at) $done++;
        if ($this->pan_verified_at) $done++;
        if ($this->bank_verified_at) $done++;
        if ($this->documents()->where('type', 'photo')->where('status', 'approved')->exists()) $done++;
        $pv = $this->latestPoliceVerification;
        if ($pv && $pv->status === 'cleared') $done++;
        return (int) round(($done / $total) * 100);
    }
}
