<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = ['payment_id', 'booking_id', 'amount', 'reason', 'status', 'processed_by', 'processed_at'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
