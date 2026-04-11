<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'address_line_1', 'address_line_2', 'landmark',
        'city_id', 'pincode', 'is_default', 'latitude', 'longitude',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
