<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    protected $fillable = ['footer_section_id', 'label', 'url', 'order', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function section()
    {
        return $this->belongsTo(FooterSection::class, 'footer_section_id');
    }
}
