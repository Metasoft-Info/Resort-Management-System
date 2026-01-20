<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResortInfo extends Model
{
    protected $table = 'resort_info';
    protected $fillable = [
        'resort_name', 'resort_tagline', 'about_text', 'mission_text', 
        'footer_description', 'address', 'phone', 'email',
        'map_embed_url', 'facilities', 'social_links', 'copyright_text',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'social_links' => 'array',
        ];
    }
}
