<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailabilitySlot extends Model
{
    protected $fillable = ['daimaa_id', 'day_of_week', 'start_time', 'end_time', 'is_available'];

    protected function casts(): array
    {
        return ['is_available' => 'boolean'];
    }

    public function daimaa()
    {
        return $this->belongsTo(User::class, 'daimaa_id');
    }

    public function dayName(): string
    {
        return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$this->day_of_week];
    }
}
