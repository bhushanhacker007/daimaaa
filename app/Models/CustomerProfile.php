<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    protected $fillable = ['user_id', 'date_of_birth', 'due_date', 'notes'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'due_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
