<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraChargeCategory extends Model
{
    protected $fillable = [
        'name',
        'price',
        'unit',
        'description',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
