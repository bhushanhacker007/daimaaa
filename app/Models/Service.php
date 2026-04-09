<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'short_description',
        'duration_minutes', 'base_price', 'icon', 'image', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_services')->withPivot('session_count');
    }
}
