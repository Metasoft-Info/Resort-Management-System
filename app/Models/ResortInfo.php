<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResortInfo extends Model
{
    protected $table = 'resort_info';
    protected $fillable = [
        'about_text', 'mission_text', 'address', 'phone', 'email',
        'map_embed_url', 'facilities', 'social_links',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'social_links' => 'array',
        ];
    }
}
