<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSession extends Model
{
    protected $fillable = [
        'booking_id', 'daimaa_id', 'service_id', 'session_number', 'scheduled_at',
        'status', 'started_at', 'completed_at', 'earning_amount', 'notes', 'start_otp',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'earning_amount' => 'decimal:2',
        ];
    }

    /**
     * Calculate and store earning for this session when completed.
     * Daimaa earns (booking total / total sessions) * daimaa_share_percent.
     */
    public function calculateEarning(): float
    {
        $booking = $this->booking;
        if (!$booking) return 0;

        $totalSessions = max(1, $booking->sessions()->count());
        $perSessionValue = (float) $booking->total_amount / $totalSessions;
        $daimaaSharePercent = config('daimaa_matching.daimaa_share_percent', 70);
        $earning = round($perSessionValue * ($daimaaSharePercent / 100), 2);

        $this->update(['earning_amount' => $earning]);

        return $earning;
    }

    public function generateOtp(): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update(['start_otp' => $otp]);
        return $otp;
    }

    public function sessionDurationMinutes(): int
    {
        if ($this->service?->duration_minutes) {
            return (int) $this->service->duration_minutes;
        }

        if ($this->booking?->booked_hours) {
            return (int) ($this->booking->booked_hours * 60);
        }

        return 60;
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function daimaa()
    {
        return $this->belongsTo(User::class, 'daimaa_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
