<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = ['daimaa_id', 'amount', 'period', 'status', 'reference', 'processed_at'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function daimaa()
    {
        return $this->belongsTo(User::class, 'daimaa_id');
    }
}
