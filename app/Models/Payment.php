<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['booking_id', 'amount', 'method', 'status', 'gateway_reference', 'gateway_response'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gateway_response' => 'array',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
