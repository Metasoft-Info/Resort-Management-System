<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonService extends Model
{
    protected $fillable = ['name', 'description', 'price', 'unit', 'category', 'service_type', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Scopes for filtering by type
    public function scopeForRoom($query)
    {
        return $query->whereIn('service_type', ['room', 'both']);
    }

    public function scopeForConvention($query)
    {
        return $query->whereIn('service_type', ['convention', 'both']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
