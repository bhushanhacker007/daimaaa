<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'short_description',
        'duration_minutes', 'base_price', 'price_per_hour', 'min_hours', 'max_hours',
        'hour_increment', 'instant_available', 'instant_surcharge',
        'icon', 'image', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'price_per_hour' => 'decimal:2',
            'min_hours' => 'decimal:1',
            'max_hours' => 'decimal:1',
            'hour_increment' => 'decimal:1',
            'instant_available' => 'boolean',
            'instant_surcharge' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function isHourlyPriced(): bool
    {
        return $this->price_per_hour !== null && $this->price_per_hour > 0;
    }

    public function getPriceForHours(float $hours): float
    {
        if ($this->isHourlyPriced()) {
            return round((float) $this->price_per_hour * $hours, 2);
        }
        return (float) $this->base_price;
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
