<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaimaaProfile extends Model
{
    protected $fillable = [
        'user_id', 'years_of_experience', 'bio', 'status', 'is_online', 'verified_at',
        'service_area_pincodes', 'home_latitude', 'home_longitude',
        'reliability_score', 'total_assignments', 'declined_assignments', 'cancelled_assignments',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'is_online' => 'boolean',
            'service_area_pincodes' => 'array',
            'home_latitude' => 'decimal:7',
            'home_longitude' => 'decimal:7',
            'reliability_score' => 'decimal:2',
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
}
