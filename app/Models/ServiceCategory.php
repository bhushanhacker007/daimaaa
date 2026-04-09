<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }
}
