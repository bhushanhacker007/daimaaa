<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['booking_id', 'customer_id', 'daimaa_id', 'rating', 'comment', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function daimaa()
    {
        return $this->belongsTo(User::class, 'daimaa_id');
    }
}
