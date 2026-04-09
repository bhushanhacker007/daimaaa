<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSession extends Model
{
    protected $fillable = [
        'booking_id', 'daimaa_id', 'session_number', 'scheduled_at',
        'status', 'started_at', 'completed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
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
