<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonService extends Model
{
    protected $fillable = ['name', 'description', 'price', 'category', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
