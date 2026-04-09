<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_number', 'customer_id', 'package_id', 'service_id', 'address_id',
        'coupon_id', 'status', 'subtotal', 'discount_amount', 'total_amount',
        'scheduled_date', 'scheduled_time', 'notes', 'cancellation_reason',
        'confirmed_at', 'completed_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'scheduled_date' => 'date',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public static function generateBookingNumber(): string
    {
        return 'DM-' . strtoupper(uniqid());
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function sessions()
    {
        return $this->hasMany(BookingSession::class);
    }

    public function assignments()
    {
        return $this->hasMany(BookingAssignment::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
