<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoliceVerification extends Model
{
    protected $fillable = [
        'daimaa_profile_id', 'status', 'initiated_by', 'initiated_at',
        'cleared_at', 'expiry_date', 'reference_number', 'agency_name',
        'report_file_path', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'initiated_at' => 'datetime',
            'cleared_at' => 'datetime',
            'expiry_date' => 'date',
        ];
    }

    public function daimaaProfile()
    {
        return $this->belongsTo(DaimaaProfile::class);
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isCleared(): bool
    {
        return $this->status === 'cleared' && !$this->isExpired();
    }

    public function statusLabel(): string
    {
        if ($this->status === 'cleared' && $this->isExpired()) return 'Expired';
        return match ($this->status) {
            'initiated' => 'Initiated',
            'in_progress' => 'In Progress',
            'cleared' => 'Cleared',
            'failed' => 'Failed',
            'expired' => 'Expired',
            default => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        if ($this->status === 'cleared' && $this->isExpired()) return 'error';
        return match ($this->status) {
            'initiated' => 'tertiary',
            'in_progress' => 'tertiary',
            'cleared' => 'primary',
            'failed' => 'error',
            'expired' => 'error',
            default => 'on-surface-variant',
        };
    }
}
