<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaimaaServiceQualification extends Model
{
    protected $fillable = ['daimaa_id', 'service_id', 'is_qualified'];

    protected function casts(): array
    {
        return ['is_qualified' => 'boolean'];
    }

    public function daimaa()
    {
        return $this->belongsTo(User::class, 'daimaa_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
