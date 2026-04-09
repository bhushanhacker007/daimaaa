<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $fillable = ['booking_id', 'itemable_type', 'itemable_id', 'quantity', 'unit_price', 'total_price'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}
