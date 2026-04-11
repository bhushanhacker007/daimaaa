<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAssignment extends Model
{
    protected $fillable = [
        'booking_id', 'daimaa_id', 'assigned_by', 'assigned_at',
        'accepted_at', 'rejected_at', 'rejection_reason',
        'match_score', 'score_breakdown', 'dispatch_rank', 'expires_at', 'dispatch_status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'expires_at' => 'datetime',
            'match_score' => 'decimal:2',
            'score_breakdown' => 'array',
        ];
    }

    public function isPending(): bool
    {
        return $this->dispatch_status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->dispatch_status === 'expired'
            || ($this->isPending() && $this->expires_at && now()->gte($this->expires_at));
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function daimaa()
    {
        return $this->belongsTo(User::class, 'daimaa_id');
    }
}
