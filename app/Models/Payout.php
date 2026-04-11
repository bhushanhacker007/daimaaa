<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'daimaa_id', 'amount', 'period', 'period_start', 'period_end',
        'sessions_count', 'status', 'reference', 'notes', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'period_start' => 'date',
            'period_end' => 'date',
            'processed_at' => 'datetime',
        ];
    }

    public function daimaa()
    {
        return $this->belongsTo(User::class, 'daimaa_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'processed' => 'Paid',
            'pending' => 'Pending',
            'failed' => 'Failed',
            default => ucfirst($this->status),
        };
    }
}
