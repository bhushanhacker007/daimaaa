<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    protected $fillable = ['pincode', 'city_id', 'is_serviceable'];

    protected function casts(): array
    {
        return ['is_serviceable' => 'boolean'];
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
