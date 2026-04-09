<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaimaaProfile extends Model
{
    protected $fillable = ['user_id', 'years_of_experience', 'bio', 'status', 'verified_at', 'service_area_pincodes'];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'service_area_pincodes' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
