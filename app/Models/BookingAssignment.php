<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAssignment extends Model
{
    protected $fillable = [
        'booking_id', 'daimaa_id', 'assigned_by', 'assigned_at',
        'accepted_at', 'rejected_at', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
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
