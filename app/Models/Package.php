<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'total_sessions', 'price',
        'discount_percent', 'is_active', 'is_featured', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'package_services')->withPivot('session_count');
    }
}
