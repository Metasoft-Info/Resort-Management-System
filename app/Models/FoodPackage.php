<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodPackage extends Model
{
    protected $fillable = ['name', 'description', 'price_per_person', 'items', 'is_active'];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
