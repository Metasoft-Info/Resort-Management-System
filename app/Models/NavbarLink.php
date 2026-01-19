<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavbarLink extends Model
{
    protected $fillable = ['label', 'url', 'order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
